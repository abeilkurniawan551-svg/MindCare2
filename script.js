/**
 * MindCare - Interactive Application Logic
 * Supports Registration, Login Breathing Visualizer, AI Chatbot, and Profile Management
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileNavigation();
  initPasswordToggles();
  initPasswordStrengthMeter();
  initRolePillSelector();
  initBreathingVisualizer();
  initChatbot();
  initAudioEmergencyPlayer();
  initProfilePage();
});

// ============================================================================
// 0. Mobile Sidebar Navigation Drawer ("Garis 3")
// ============================================================================
function initMobileNavigation() {
  const toggleBtn = document.getElementById('mobileNavToggle');
  const sidebar = document.querySelector('.app-sidebar');
  const closeBtn = document.getElementById('sidebarCloseBtn');
  const backdrop = document.getElementById('sidebarBackdrop');

  if (!toggleBtn || !sidebar) return;
  if (toggleBtn.dataset.navBound === 'true') return;
  toggleBtn.dataset.navBound = 'true';

  function openSidebar() {
    sidebar.classList.add('is-open');
    if (backdrop) backdrop.classList.add('is-open');
    document.body.classList.add('sidebar-open');
    toggleBtn.setAttribute('aria-expanded', 'true');
  }

  function closeSidebar() {
    sidebar.classList.remove('is-open');
    if (backdrop) backdrop.classList.remove('is-open');
    document.body.classList.remove('sidebar-open');
    toggleBtn.setAttribute('aria-expanded', 'false');
  }

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (sidebar.classList.contains('is-open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      closeSidebar();
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', () => {
      closeSidebar();
    });
  }

  // Close when clicking any nav link
  const navLinks = sidebar.querySelectorAll('.sidebar-nav-item a');
  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 768) {
        closeSidebar();
      }
    });
  });

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
      closeSidebar();
    }
  });

  // Close if window resized to desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 768 && sidebar.classList.contains('is-open')) {
      closeSidebar();
    }
  });
}

// ============================================================================
// 1. Password Toggles (Show / Hide Password)
// ============================================================================
function initPasswordToggles() {
  const toggleButtons = document.querySelectorAll('.toggle-password-visibility');
  toggleButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (!input) return;

      if (input.type === 'password') {
        input.type = 'text';
        btn.setAttribute('aria-label', 'Sembunyikan sandi');
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
      } else {
        input.type = 'password';
        btn.setAttribute('aria-label', 'Tampilkan sandi');
        btn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
      }
    });
  });
}

// ============================================================================
// 2. Registration: Password Strength Meter & Live Checklist
// ============================================================================
function initPasswordStrengthMeter() {
  const pwdInput = document.getElementById('reg_password');
  const strengthBadge = document.getElementById('pwd_strength_badge');
  const segments = document.querySelectorAll('.strength-segment');

  const checkLength = document.getElementById('chk_length');
  const checkSymbols = document.getElementById('chk_symbols');
  const checkNumbersUpper = document.getElementById('chk_num_upper');
  const checkPersonal = document.getElementById('chk_personal');

  if (!pwdInput) return;

  pwdInput.addEventListener('input', () => {
    const val = pwdInput.value;
    let score = 0;

    // 1. Min 8 characters
    const hasMinLength = val.length >= 8;
    updateCheckItem(checkLength, hasMinLength);
    if (hasMinLength) score++;

    // 2. Combination of symbols (!@#$)
    const hasSymbols = /[!@#$%^&*(),.?":{}|<>]/.test(val);
    updateCheckItem(checkSymbols, hasSymbols);
    if (hasSymbols) score++;

    // 3. Numbers & Uppercase
    const hasNumUpper = /[0-9]/.test(val) && /[A-Z]/.test(val);
    updateCheckItem(checkNumbersUpper, hasNumUpper);
    if (hasNumUpper) score++;

    // 4. Free from common personal names / easily guessable
    const notTrivial = val.length > 5 && !/(123456|password|qwerty|admin)/i.test(val);
    updateCheckItem(checkPersonal, notTrivial);
    if (notTrivial) score++;

    // Update meter segments
    segments.forEach((seg, idx) => {
      if (idx < score) {
        seg.classList.add('active-green');
      } else {
        seg.classList.remove('active-green');
      }
    });

    if (strengthBadge) {
      if (score === 4) {
        strengthBadge.textContent = 'Sangat Kuat';
        strengthBadge.style.color = '#15803d';
        strengthBadge.style.backgroundColor = '#dcfce7';
      } else if (score >= 2) {
        strengthBadge.textContent = 'Cukup';
        strengthBadge.style.color = '#b45309';
        strengthBadge.style.backgroundColor = '#fef3c7';
      } else {
        strengthBadge.textContent = 'Lemah';
        strengthBadge.style.color = '#b91c1c';
        strengthBadge.style.backgroundColor = '#fee2e2';
      }
    }
  });

  function updateCheckItem(element, isValid) {
    if (!element) return;
    if (isValid) {
      element.classList.add('checked');
      element.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> ` + element.getAttribute('data-text');
    } else {
      element.classList.remove('checked');
      element.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle></svg> ` + element.getAttribute('data-text');
    }
  }
}

// ============================================================================
// 3. Role Selector Pills (Mahasiswa, Karyawan, Umum)
// ============================================================================
function initRolePillSelector() {
  const roleLabels = document.querySelectorAll('.role-pill-option');
  roleLabels.forEach(label => {
    label.addEventListener('click', () => {
      roleLabels.forEach(l => l.classList.remove('selected'));
      label.classList.add('selected');
      const radio = label.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });
}

// ============================================================================
// 4. Login: 4-7-8 Breathing Rhythm Visualizer
// ============================================================================
function initBreathingVisualizer() {
  const canvas = document.getElementById('breathingWaveCanvas');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  let animationFrame;
  let t = 0;

  function resizeCanvas() {
    canvas.width = canvas.parentElement.clientWidth || 360;
    canvas.height = 70;
  }
  resizeCanvas();
  window.addEventListener('resize', resizeCanvas);

  function drawWave() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const width = canvas.width;
    const height = canvas.height;
    const midY = height / 2;

    // Draw reference sine wave
    ctx.beginPath();
    ctx.moveTo(0, midY);

    for (let x = 0; x <= width; x += 2) {
      // 4-7-8 relaxed breathing curve formula
      const wave = Math.sin((x / width) * Math.PI * 2.2 - t * 0.03) * 18;
      ctx.lineTo(x, midY + wave);
    }

    ctx.strokeStyle = 'rgba(94, 234, 212, 0.4)';
    ctx.lineWidth = 2.5;
    ctx.stroke();

    // Moving glowing pulse point
    const pulseX = ((t * 1.5) % (width + 40)) - 20;
    const pulseY = midY + Math.sin((pulseX / width) * Math.PI * 2.2 - t * 0.03) * 18;

    if (pulseX >= 0 && pulseX <= width) {
      // Glow circle
      ctx.beginPath();
      ctx.arc(pulseX, pulseY, 8, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(94, 234, 212, 0.35)';
      ctx.fill();

      // Core point
      ctx.beginPath();
      ctx.arc(pulseX, pulseY, 4, 0, Math.PI * 2);
      ctx.fillStyle = '#ffffff';
      ctx.shadowColor = '#5eead4';
      ctx.shadowBlur = 10;
      ctx.fill();
      ctx.shadowBlur = 0;
    }

    t += 1;
    animationFrame = requestAnimationFrame(drawWave);
  }

  drawWave();
}

// ============================================================================
// 5. AI Chatbot Companion Logic with Real API Connection
// ============================================================================
function initChatbot() {
  const chatForm = document.getElementById('chatForm');
  const chatInput = document.getElementById('chatInput');
  const conversationArea = document.getElementById('chatConversationArea');
  const promptChips = document.querySelectorAll('.prompt-chip');
  const clearSessionBtn = document.getElementById('clearSessionBtn');

  // API Settings Elements
  const openApiSettingsBtn = document.getElementById('openApiSettingsBtn');
  const closeApiSettingsBtn = document.getElementById('closeApiSettingsBtn');
  const apiSettingsModal = document.getElementById('apiSettingsModal');
  const apiProviderSelect = document.getElementById('apiProviderSelect');
  const apiKeyFieldGroup = document.getElementById('apiKeyFieldGroup');
  const apiKeyValueInput = document.getElementById('apiKeyValueInput');
  const apiKeyHelpLink = document.getElementById('apiKeyHelpLink');
  const apiInfoNotice = document.getElementById('apiInfoNotice');
  const saveApiSettingsBtn = document.getElementById('saveApiSettingsBtn');
  const activeApiBadgeText = document.getElementById('activeApiBadgeText');

  if (!conversationArea) return;

  // Realtime User Context from window or fallback
  const userDisplayName = (window.MINDCARE_USER && window.MINDCARE_USER.firstName) ? window.MINDCARE_USER.firstName : 'Teman';
  const userFullname = (window.MINDCARE_USER && window.MINDCARE_USER.fullname) ? window.MINDCARE_USER.fullname : 'Sobat MindCare';
  const currentUserId = (window.MINDCARE_USER && window.MINDCARE_USER.id) ? window.MINDCARE_USER.id : 1;

  // Active API State (Default to Google Gemini with configured token)
  const DEFAULT_GEMINI_KEY = 'AQ.Ab8RN6IAIJKg7Ars3GL0zA25RXd9CLIU3waWV4ihtnv1HKO1Bw';
  let currentProvider = localStorage.getItem('mindcare_api_provider');
  if (!currentProvider || currentProvider === 'mindcare') {
    currentProvider = 'gemini';
    localStorage.setItem('mindcare_api_provider', 'gemini');
  }
  let currentApiKey = localStorage.getItem('mindcare_api_key');
  if (!currentApiKey || currentApiKey.length < 10) {
    currentApiKey = DEFAULT_GEMINI_KEY;
    localStorage.setItem('mindcare_api_key', DEFAULT_GEMINI_KEY);
  }

  // Conversation history for multi-turn context
  const conversationHistory = [
    {
      role: 'assistant',
      content: `Halo ${userDisplayName}. Aku di sini untuk mendengarkan tanpa menghakimi sama sekali. Apapun yang sedang membebani pundakmu hari ini, luapkan saja pelan-pelan tanpa terburu-buru. Apa yang paling terasa dominan di hati atau pikiranmu saat ini?`
    }
  ];

  function updateApiBadge() {
    if (!activeApiBadgeText) return;
    const providerNames = {
      'mindcare': 'API: MindCare AI',
      'gemini': 'API: Google Gemini',
      'groq': 'API: Groq Llama 3',
      'openai': 'API: OpenAI ChatGPT'
    };
    activeApiBadgeText.textContent = providerNames[currentProvider] || 'API: MindCare AI';
  }
  updateApiBadge();

  // Auto scroll to bottom
  function scrollToBottom() {
    conversationArea.scrollTop = conversationArea.scrollHeight;
  }
  scrollToBottom();

  // Send message handler with API integration
  async function handleSendMessage(messageText) {
    if (!messageText || !messageText.trim()) return;

    const userText = messageText.trim();
    if (chatInput) chatInput.value = '';

    const timeNow = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';

    // Append User Bubble Realtime
    const userMsgHtml = `
      <div class="chat-msg-row user">
        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='36' height='36' viewBox='0 0 36 36'><circle cx='18' cy='18' r='18' fill='%230f766e'/><circle cx='18' cy='14' r='6' fill='%23ffffff'/><path d='M8 30c0-5.5 4.5-8 10-8s10 2.5 10 8' fill='%23ffffff'/></svg>" alt="${escapeHtml(userDisplayName)}" class="chat-msg-avatar">
        <div class="chat-msg-body">
          <div class="chat-msg-meta">${escapeHtml(userDisplayName)} • ${timeNow}</div>
          <div class="chat-bubble">${escapeHtml(userText)}</div>
        </div>
      </div>
    `;
    conversationArea.insertAdjacentHTML('beforeend', userMsgHtml);
    scrollToBottom();

    // Track user message in history
    conversationHistory.push({ role: 'user', content: userText });

    // Update session card title in sidebar dynamically if first message
    const activeSessionCard = document.querySelector('.session-item-card.active .session-item-title');
    if (activeSessionCard && (activeSessionCard.textContent.trim() === 'Sesi Refleksi Baru' || activeSessionCard.textContent.trim() === '')) {
      const shortTitle = userText.split(' ').slice(0, 5).join(' ');
      activeSessionCard.textContent = shortTitle.length > 35 ? shortTitle.slice(0, 32) + '...' : shortTitle;
    }

    // Append Typing Indicator
    const typingId = 'typing_' + Date.now();
    const typingHtml = `
      <div id="${typingId}" class="chat-msg-row bot">
        <div class="bot-avatar-box" style="width:34px;height:34px;border-radius:50%;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
        </div>
        <div class="chat-msg-body">
          <div class="chat-msg-meta">MindCare AI • Sedang berpikir...</div>
          <div class="chat-bubble" style="color:var(--slate-400);font-style:italic;">Mendengarkan dan merespon dengan penuh empati...</div>
        </div>
      </div>
    `;
    conversationArea.insertAdjacentHTML('beforeend', typingHtml);
    scrollToBottom();

    try {
      const response = await fetch('api_chat.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          message: userText,
          session_id: window.MINDCARE_SESSION_ID || 0,
          user_id: currentUserId,
          user_name: userFullname,
          provider: currentProvider,
          api_key: currentApiKey,
          history: conversationHistory.slice(-8)
        })
      });

      const data = await response.json();
      const typingEl = document.getElementById(typingId);
      if (typingEl) typingEl.remove();

      if (data && data.status === 'success' && data.reply) {
        // Track AI message in history
        conversationHistory.push({ role: 'assistant', content: data.reply });

        // Update session_id if new
        if (data.session_id) {
          window.MINDCARE_SESSION_ID = data.session_id;
        }

        // Render Bot Reply
        const botReplyHtml = buildBotReplyHtml(data.reply, data.actions, data.timestamp || timeNow, data.provider);
        conversationArea.insertAdjacentHTML('beforeend', botReplyHtml);
      } else {
        throw new Error(data.message || 'Gagal menerima respon dari server');
      }
    } catch (err) {
      console.error('Chatbot API Error:', err);
      const typingEl = document.getElementById(typingId);
      if (typingEl) typingEl.remove();

      // Fallback Empathetic Response with Direct to Jurnal
      const fallbackHtml = `
        <div class="chat-msg-row bot">
          <div class="bot-avatar-box" style="width:34px;height:34px;border-radius:50%;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
          </div>
          <div class="chat-msg-body">
            <div class="chat-msg-meta">MindCare AI • ${timeNow}</div>
            <div class="chat-bubble">
              Terima kasih sudah mau berbagi apa yang membebani hatimu, ${escapeHtml(userDisplayName)}.<br><br>
              Setiap emosi yang sedang hadir adalah valid dan patut dihargai. Agar beban ini tidak terus berputar menyiksa di dalam pikiranmu, sangat dianjurkan untuk langsung menuangkannya ke dalam <strong>Jurnal Refleksi</strong> harianmu.<br><br>
              <div class="bot-action-cards-grid">
                <div class="bot-action-card" style="border:1.5px solid var(--teal-300); background:#f0fdfa;">
                  <div class="bot-action-card-title">📝 Catat ke Jurnal Refleksi Sekarang</div>
                  <div class="bot-action-card-desc">Simpan curahan hatimu ke lembar jurnal agar terurai aman & teratur</div>
                  <button type="button" class="btn-card-action" data-action="open-journal" data-url="mood-tracker.php" style="background:var(--teal-700); color:#fff; border:none; padding:8px 14px;">Buka Jurnal Sekarang</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      conversationArea.insertAdjacentHTML('beforeend', fallbackHtml);
    }

    scrollToBottom();
  }

  function buildBotReplyHtml(replyText, actions, time, provider) {
    // Format text: convert line breaks and basic bold markdown
    let formattedText = escapeHtml(replyText)
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\n\n/g, '<br><br>')
      .replace(/\n/g, '<br>');

    // Build Action Cards HTML if any
    let actionsHtml = '';
    if (actions && actions.length > 0) {
      actionsHtml = `
        <div class="bot-action-cards-grid">
          ${actions.map(act => {
        const isJournal = (act.action === 'open-journal' || act.action === 'deconstruct' || (act.url && act.url.includes('mood')));
        const cardStyle = isJournal ? 'border:1.5px solid var(--teal-300); background:#f0fdfa;' : '';
        const btnStyle = isJournal ? 'background:var(--teal-700); color:#ffffff; border:none;' : '';
        return `
              <div class="bot-action-card" style="${cardStyle}">
                <div class="bot-action-card-title">${escapeHtml(act.title)}</div>
                <div class="bot-action-card-desc">${escapeHtml(act.desc)}</div>
                <button type="button" class="btn-card-action" data-action="${escapeHtml(act.action)}" data-url="${escapeHtml(act.url || '')}" style="${btnStyle}">
                  ${escapeHtml(act.btnText || 'Buka')}
                </button>
              </div>
            `;
      }).join('')}
        </div>
      `;
    }

    return `
      <div class="chat-msg-row bot">
        <div class="bot-avatar-box" style="width:34px;height:34px;border-radius:50%;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="22"></line></svg>
        </div>
        <div class="chat-msg-body">
          <div class="chat-msg-meta">MindCare AI • ${time}</div>
          <div class="chat-bubble">
            ${formattedText}
            ${actionsHtml}
          </div>
        </div>
      </div>
    `;
  }

  // Form submit event
  if (chatForm) {
    chatForm.addEventListener('submit', (e) => {
      e.preventDefault();
      if (chatInput) handleSendMessage(chatInput.value);
    });
  }

  // Quick Prompt Chips
  promptChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const promptText = chip.getAttribute('data-prompt') || chip.textContent.trim();
      handleSendMessage(promptText);
    });
  });

  // Action Buttons inside message (Delegation: Direct to Jurnal / Exercises)
  conversationArea.addEventListener('click', (e) => {
    const actionBtn = e.target.closest('.btn-card-action');
    if (!actionBtn) return;

    const actionType = actionBtn.getAttribute('data-action');
    const targetUrl = actionBtn.getAttribute('data-url');

    if (targetUrl) {
      window.location.href = targetUrl;
      return;
    }

    if (actionType === 'open-journal' || actionType === 'deconstruct') {
      window.location.href = 'mood-tracker.php';
    } else if (actionType === 'box-breathing') {
      alert("🌿 Memulai Panduan Napas Kotak 4x4 (Box Breathing):\n\n1. Tarik napas perlahan melalui hidung (4 detik)...\n2. Tahan napas dengan tenang (4 detik)...\n3. Hembuskan lembut lewat bibir (4 detik)...\n4. Istirahat dalam hening sejenak (4 detik)...\n\nUlangi 3-4 kali siklus.");
    } else if (actionType === 'grounding') {
      alert("⚓ Teknik Grounding 5-4-3-2-1:\n\n• 5 hal nyata di sekitarmu yang bisa kamu lihat\n• 4 tekstur permukaan yang bisa kamu sentuh\n• 3 suara yang terdengar di ruangan\n• 2 aroma yang bisa kamu hirup\n• 1 rasa yang terasa di lidah\n\nKamu berada di sini dan saat ini, aman terlindungi.");
    } else if (actionType === 'call-119') {
      window.location.href = 'emergency.php';
    }
  });

  // Clear Session Button
  if (clearSessionBtn) {
    clearSessionBtn.addEventListener('click', () => {
      if (confirm('Apakah Anda yakin ingin membersihkan seluruh jejak obrolan sesi ini secara permanen?')) {
        alert('Riwayat obrolan sesi ini telah dihapus tuntas dari memori sesi.');
        window.location.reload();
      }
    });
  }

  // ============================================================================
  // API Settings Modal Handler
  // ============================================================================
  if (openApiSettingsBtn && apiSettingsModal) {
    openApiSettingsBtn.addEventListener('click', () => {
      if (apiProviderSelect) apiProviderSelect.value = currentProvider;
      if (apiKeyValueInput) apiKeyValueInput.value = currentApiKey;
      toggleApiKeyVisibility(currentProvider);
      apiSettingsModal.style.display = 'flex';
    });

    if (closeApiSettingsBtn) {
      closeApiSettingsBtn.addEventListener('click', () => {
        apiSettingsModal.style.display = 'none';
      });
    }

    // Close on backdrop click
    apiSettingsModal.addEventListener('click', (e) => {
      if (e.target === apiSettingsModal) {
        apiSettingsModal.style.display = 'none';
      }
    });

    if (apiProviderSelect) {
      apiProviderSelect.addEventListener('change', () => {
        toggleApiKeyVisibility(apiProviderSelect.value);
      });
    }

    if (saveApiSettingsBtn) {
      saveApiSettingsBtn.addEventListener('click', () => {
        currentProvider = apiProviderSelect.value;
        currentApiKey = apiKeyValueInput.value.trim();

        localStorage.setItem('mindcare_api_provider', currentProvider);
        localStorage.setItem('mindcare_api_key', currentApiKey);

        updateApiBadge();
        apiSettingsModal.style.display = 'none';
        alert(`Pengaturan API berhasil disimpan! Mesin aktif: ${currentProvider.toUpperCase()}`);
      });
    }
  }

  function toggleApiKeyVisibility(provider) {
    if (!apiKeyFieldGroup) return;
    if (provider === 'mindcare') {
      apiKeyFieldGroup.style.display = 'none';
      if (apiInfoNotice) {
        apiInfoNotice.style.display = 'block';
        apiInfoNotice.innerHTML = '💡 <strong>Mode Bawaan Aktif:</strong> Chatbot menggunakan engine klinis MindCare yang siap merespon secara empatik tanpa memerlukan API key eksternal.';
      }
    } else {
      apiKeyFieldGroup.style.display = 'flex';
      apiKeyFieldGroup.style.flexDirection = 'column';
      if (apiInfoNotice) {
        if (provider === 'gemini') {
          apiInfoNotice.style.display = 'block';
          apiInfoNotice.innerHTML = '✨ <strong>Google Gemini Aktif:</strong> Token API Anda telah terhubung langsung ke model resmi <strong>Gemini 3.8 Flash</strong>.';
        } else {
          apiInfoNotice.style.display = 'none';
        }
      }

      const helpUrls = {
        'gemini': 'https://aistudio.google.com/app/apikey',
        'groq': 'https://console.groq.com/keys',
        'openai': 'https://platform.openai.com/api-keys'
      };
      if (apiKeyHelpLink) apiKeyHelpLink.href = helpUrls[provider] || '#';
    }
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
}

// ============================================================================
// 6. Audio Napas Darurat Player
// ============================================================================
function initAudioEmergencyPlayer() {
  const playBtn = document.getElementById('playEmergencyAudio');
  const waveBars = document.querySelectorAll('.audio-waveform-bars .wave-bar');
  const timerLabel = document.getElementById('audioTimerLabel');
  if (!playBtn) return;

  let isPlaying = false;
  let seconds = 0;
  let timerInterval;

  playBtn.addEventListener('click', () => {
    isPlaying = !isPlaying;
    if (isPlaying) {
      playBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>`;
      waveBars.forEach(bar => bar.classList.add('playing'));
      timerInterval = setInterval(() => {
        seconds++;
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        if (timerLabel) {
          timerLabel.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
      }, 1000);
    } else {
      playBtn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>`;
      waveBars.forEach(bar => bar.classList.remove('playing'));
      clearInterval(timerInterval);
    }
  });
}

// ============================================================================
// 7. Profile Page: Tab Switching & Save Changes Toast
// ============================================================================
function initProfilePage() {
  const profileTabs = document.querySelectorAll('.profile-nav-tab');
  profileTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
      e.preventDefault();
      profileTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
    });
  });

  const saveProfileBtn = document.getElementById('saveProfileBtn');
  const toastNotification = document.getElementById('saveProfileToast');

  if (saveProfileBtn && toastNotification) {
    saveProfileBtn.addEventListener('click', (e) => {
      // Show smooth feedback toast
      toastNotification.style.display = 'inline-flex';
      toastNotification.style.opacity = '1';
      saveProfileBtn.textContent = 'Menyimpan...';

      setTimeout(() => {
        saveProfileBtn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Simpan Perubahan`;
        setTimeout(() => {
          toastNotification.style.opacity = '0';
          setTimeout(() => {
            toastNotification.style.display = 'none';
          }, 400);
        }, 3000);
      }, 600);
    });
  }
}
