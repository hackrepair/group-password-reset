document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('#gpr-reset-form');
  const progressPanel = document.querySelector('#gpr-progress-panel');
  const progressStatus = progressPanel?.querySelector('.gpr-progress__status');
  const progressBar = progressPanel?.querySelector('.gpr-progress__bar');
  const progressMeta = progressPanel?.querySelector('.gpr-progress__meta');
  const resultsPanel = document.querySelector('#gpr-results-panel');
  const resultsBody = document.querySelector('#gpr-results-body');
  const resultsSummary = document.querySelector('#gpr-results-summary');
  const resultsTable = document.querySelector('#gpr-results-table');
  const resultsNote = document.querySelector('#gpr-results-note');
  const emailWarning = document.querySelector('#gpr-email-warning');
  let currentJobId = null;

  if (!form || !window.fetch || !window.gprAdmin) {
    return;
  }

  const notifyForm = document.querySelector('#gpr-notify-form');
  const notifyStatus = document.querySelector('#gpr-notify-status');
  const notifyAll = document.querySelector('#gpr-notify-all');
  const recipientBoxes = [...(notifyForm?.querySelectorAll('input[name="recipients[]"]') || [])];
  const syncRecipientSelection = () => {
    if (!notifyAll) return;
    const selected = recipientBoxes.filter((box) => box.checked).length;
    notifyAll.checked = recipientBoxes.length > 0 && selected === recipientBoxes.length;
    notifyAll.indeterminate = selected > 0 && selected < recipientBoxes.length;
  };
  notifyAll?.addEventListener('change', () => {
    recipientBoxes.forEach((box) => { box.checked = notifyAll.checked; });
    syncRecipientSelection();
  });
  recipientBoxes.forEach((box) => box.addEventListener('change', syncRecipientSelection));
  syncRecipientSelection();
  notifyForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = notifyForm.querySelector('button[type="submit"]');
    const body = new URLSearchParams(new FormData(notifyForm));
    button.disabled = true;
    notifyStatus.textContent = window.gprAdmin.messages.sendingSummary;
    try {
      const response = await fetch(window.gprAdmin.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.data || window.gprAdmin.messages.requestFailed);
      notifyStatus.textContent = data.data;
    } catch (error) {
      notifyStatus.textContent = error.message;
    } finally {
      button.disabled = false;
    }
  });

  async function refreshNotificationSummary() {
    try {
      const summary = await postAction('gpr_notification_summary', {});
      notifyForm.hidden = !summary;
      document.querySelector('#gpr-notify-empty').hidden = !!summary;
      if (summary) {
        notifyForm.elements.report.value = summary.id;
        document.querySelector('#gpr-notify-preview').textContent = summary.text;
      }
    } catch (error) {
      notifyStatus.textContent = error.message;
    }
  }

  const skipEmailNotificationsField = form.querySelector('[name="gpr_skip_email_notifications"]');

  if (skipEmailNotificationsField) {
    skipEmailNotificationsField.addEventListener('change', toggleEmailWarning);
    toggleEmailWarning();
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const scopeField = form.querySelector('[name="gpr_user_role"]');
    const role = scopeField.value;
    const excludedUsernames = form.querySelector('[name="gpr_excluded_usernames"]').value;
    const confirmation = form.querySelector('[name="gpr_confirm_reset"]');
    const skipEmailNotifications = skipEmailNotificationsField;

    if (!scopeField.checkValidity()) {
      scopeField.reportValidity();
      return;
    }

    if (!confirmation.checked) {
      confirmation.reportValidity();
      return;
    }

    notifyForm.hidden = true;
    notifyStatus.textContent = '';
    toggleBusyState(true);
    setProgress(0, window.gprAdmin.messages.preparingJob, '');
    resultsBody.innerHTML = '';
    resultsSummary.innerHTML = '';
    resultsPanel.hidden = false;
    if (resultsTable) {
      resultsTable.hidden = true;
    }
    if (resultsNote) {
      resultsNote.hidden = true;
      resultsNote.textContent = '';
    }

    try {
      const startResponse = await postAction('gpr_start_job', {
        role,
        excluded_usernames: excludedUsernames,
        gpr_confirm_reset: confirmation.checked ? '1' : '',
        gpr_skip_email_notifications: skipEmailNotifications?.checked ? '1' : '',
      });

      currentJobId = startResponse.jobId || null;
      renderResults(startResponse.results || []);
      renderSummary(startResponse);

      if (!startResponse.hasQueuedUsers) {
        setProgress(100, window.gprAdmin.messages.complete, window.gprAdmin.messages.noQueuedUsers);
        toggleBusyState(false);
        await refreshNotificationSummary();
        return;
      }

      await processJob();
    } catch (error) {
      setProgress(0, window.gprAdmin.messages.startError, error.message);
      toggleBusyState(false);
    }
  });

  async function processJob() {
    if (!currentJobId) {
      throw new Error(window.gprAdmin.messages.requestFailed);
    }

    try {
      const response = await postAction('gpr_process_job', {
        job_id: currentJobId,
      });

      renderResults(response.results || []);
      renderSummary(response);
      setProgressFromSummary(response.summary);

      if (response.completed) {
        currentJobId = null;
        setProgress(100, window.gprAdmin.messages.complete, buildProgressMeta(response.summary));
        toggleBusyState(false);
        await refreshNotificationSummary();
        return;
      }

      window.setTimeout(processJob, 250);
    } catch (error) {
      currentJobId = null;
      setProgress(progressBar.value || 0, window.gprAdmin.messages.processError, error.message);
      toggleBusyState(false);
    }
  }

  async function postAction(action, payload) {
    const body = new URLSearchParams({
      action,
      nonce: window.gprAdmin.nonce,
      ...payload,
    });

    const response = await fetch(window.gprAdmin.ajaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: body.toString(),
      credentials: 'same-origin',
    });

    const data = await response.json();

    if (!response.ok || !data.success) {
      throw new Error(data?.data || window.gprAdmin.messages.requestFailed);
    }

    return data.data;
  }

  function renderResults(results) {
    if (results.length && resultsTable) {
      resultsTable.hidden = false;
    }

    results.forEach((result) => {
      const row = document.createElement('tr');
      const statusClass = `gpr-status gpr-status--${result.status}`;

      row.innerHTML = `
        <td>${escapeHtml(result.username)}</td>
        <td>${escapeHtml(result.email)}</td>
        <td>${escapeHtml(result.role)}</td>
        <td><span class="${statusClass}">${escapeHtml(capitalize(result.status))}</span></td>
        <td>${escapeHtml(result.message)}</td>
      `;

      resultsBody.appendChild(row);
      while (resultsBody.children.length > 20) {
        resultsBody.firstElementChild.remove();
      }
    });
  }

  function renderSummary(payload) {
    const summary = payload.summary;

    if (resultsNote) {
      const shouldShowNote = payload.details_available === false;
      resultsNote.hidden = !shouldShowNote;
      resultsNote.textContent = shouldShowNote ? payload.details_notice || '' : '';
    }

    resultsSummary.innerHTML = `
      <div class="gpr-summary-grid">
        <div><strong>${escapeHtml(window.gprAdmin.messages.scope)}:</strong> ${escapeHtml(payload.scope_label || window.gprAdmin.messages.allUsers)}</div>
        <div class="gpr-metric gpr-metric--matched"><strong>${summary.total}</strong><span>${escapeHtml(window.gprAdmin.messages.matchedUsers)}</span></div>
        <div class="gpr-metric gpr-metric--processed"><strong>${summary.processed}</strong><span>${escapeHtml(window.gprAdmin.messages.processed)}</span></div>
        <div class="gpr-metric gpr-metric--success"><strong>${summary.success}</strong><span>${escapeHtml(window.gprAdmin.messages.success)}</span></div>
        <div class="gpr-metric gpr-metric--failed"><strong>${summary.failed}</strong><span>${escapeHtml(window.gprAdmin.messages.failed)}</span></div>
        <div class="gpr-metric gpr-metric--skipped"><strong>${summary.skipped}</strong><span>${escapeHtml(window.gprAdmin.messages.skipped)}</span></div>
      </div>
      <p><strong>${escapeHtml(window.gprAdmin.messages.notificationMode)}:</strong> ${escapeHtml(payload.notification_label || '')}</p>
      <p><strong>${escapeHtml(window.gprAdmin.messages.excludedAccounts)}:</strong> ${escapeHtml(payload.excluded_count ?? 0)}</p>
    `;
  }

  function setProgressFromSummary(summary) {
    const processed = Number(summary.processed || 0);
    const total = Number(summary.total || 0);
    const percent = total > 0 ? Math.round((processed / total) * 100) : 100;

    setProgress(percent, window.gprAdmin.messages.processingJob, buildProgressMeta(summary));
  }

  function buildProgressMeta(summary) {
    return `${summary.processed}/${summary.total} processed, ${summary.success} success, ${summary.failed} failed, ${summary.skipped} skipped`;
  }

  function setProgress(percent, status, meta) {
    progressPanel.hidden = false;
    progressBar.value = percent;
    progressStatus.textContent = status;
    progressStatus.classList.toggle('gpr-progress__status--complete', status === window.gprAdmin.messages.complete);
    progressMeta.textContent = meta;
  }

  function toggleBusyState(isBusy) {
    form.querySelectorAll('button, input, select, textarea').forEach((field) => {
      field.disabled = isBusy;
    });
  }

  function toggleEmailWarning() {
    if (!emailWarning || !skipEmailNotificationsField) {
      return;
    }

    emailWarning.hidden = skipEmailNotificationsField.checked;
  }

  function capitalize(value) {
    return value.charAt(0).toUpperCase() + value.slice(1);
  }

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }
});
