
let tasks = window.SF_TASKS || [
  { id: 1, title: 'SEN4002 PORT1 Report', desc: 'Phase 02 portfolio report', subject: 'SEN4002', priority: 'high', type: 'assignment', due: '2026-02-28', progress: 65, done: false },
  { id: 2, title: 'Database ER Diagram', desc: 'Complete entity-relationship model', subject: 'DBS3001', priority: 'medium', type: 'task', due: '2026-03-05', progress: 30, done: false },
  { id: 3, title: 'Web Portfolio Website', desc: 'Build personal portfolio site', subject: 'WEB2001', priority: 'low', type: 'assignment', due: '2026-03-12', progress: 80, done: false },
  { id: 4, title: 'Unit Testing Lab Report', desc: 'Write unit tests for the web app', subject: 'SEN4002', priority: 'medium', type: 'lab', due: '2026-02-20', progress: 100, done: true },
  { id: 5, title: 'Maths Exam Revision', desc: 'Revise chapters 3-6', subject: 'MAT1001', priority: 'high', type: 'exam', due: '2026-03-08', progress: 20, done: false },
];

let announcements = window.SF_ANNOUNCEMENTS || [
  { id: 1, title: 'Semester 2 Timetable Released', body: 'The updated timetable for Semester 2 is now available on the student portal.', priority: 'urgent', date: '2026-02-20' },
  { id: 2, title: 'Library Extended Hours', body: 'The library will be open until 11pm from Monday to Friday until end of semester.', priority: 'normal', date: '2026-02-18' },
  { id: 3, title: 'SEN4002 PORT1 Submission Reminder', body: 'Reminder: PORT1 (Phase 02) is due on 28th February by 2:00pm via Turnitin.', priority: 'urgent', date: '2026-02-15' },
];

let subjects = window.SF_SUBJECTS || [
  { code: 'SEN4002', name: 'Software Design & Dev', icon: '💻', color: '#4f8ef7' },
  { code: 'DBS3001', name: 'Database Systems', icon: '🗄️', color: '#10b981' },
  { code: 'WEB2001', name: 'Web Development', icon: '🌐', color: '#f59e0b' },
  { code: 'MAT1001', name: 'Mathematics', icon: '📐', color: '#ef4444' },
];

let nextId = 100;
let editingId = null;
let donutChart, barChart;

function showPage(name, btn) {
  document.querySelectorAll('.page-section').forEach(function (p) {
    p.classList.remove('active');
  });
  document.querySelectorAll('.nav-link').forEach(function (b) {
    b.classList.remove('active-student');
  });

  var section = document.getElementById('page-' + name);
  if (section) section.classList.add('active');
  if (btn) btn.classList.add('active-student');

  var titles = {
    dashboard: 'Dashboard <span style="color:var(--primary)">Overview</span>',
    tasks: 'My <span style="color:var(--primary)">Tasks</span>',
    assignments: 'Assignments <span style="color:var(--primary)">Tracker</span>',
    subjects: 'My <span style="color:var(--primary)">Subjects</span>',
    announcements: '📢 <span style="color:var(--primary)">Announcements</span>',
    profile: 'My <span style="color:var(--primary)">Profile</span>'
  };
  var titleEl = document.getElementById('topbar-title');
  if (titleEl) titleEl.innerHTML = titles[name] || name;

  if (name === 'dashboard') renderDashboard();
  if (name === 'tasks') renderTaskList();
  if (name === 'assignments') renderAssignments();
  if (name === 'subjects') renderSubjects();
  if (name === 'announcements') renderAnnouncements();
}

function renderDashboard() {
  var today = new Date(); today.setHours(0, 0, 0, 0);
  var total = tasks.length;
  var done = tasks.filter(function (t) { return t.done; }).length;
  var pending = tasks.filter(function (t) { return !t.done; }).length;
  var overdue = tasks.filter(function (t) { return !t.done && t.due && new Date(t.due) < today; }).length;

  setText('stat-total', total);
  setText('stat-pending', pending);
  setText('stat-done', done);
  setText('stat-overdue', overdue);
  setText('stat-pending-hero', pending);

  var h = new Date().getHours();
  var greeting = (h < 12 ? 'Good Morning' : h < 18 ? 'Good Afternoon' : 'Good Evening');
  var userName = (window.SF_USER_NAME || 'Student');
  setText('greeting', greeting + ', ' + userName + '! 👋');

  var weekEnd = new Date(today); weekEnd.setDate(weekEnd.getDate() + 7);
  var dueSoon = tasks.filter(function (t) {
    return !t.done && t.due && new Date(t.due) >= today && new Date(t.due) <= weekEnd;
  });
  var el = document.getElementById('due-soon-list');
  if (el) {
    el.innerHTML = dueSoon.length
      ? dueSoon.map(taskItemHTML).join('')
      : '<div class="empty-state"><div class="empty-icon">🎉</div><h3>All clear!</h3><p>No tasks due in the next 7 days.</p></div>';
  }

  var ap = document.getElementById('announce-preview');
  if (ap) {
    ap.innerHTML = announcements.slice(0, 2).map(function (a) {
      return '<div class="announce-card announce-card-student ' + (a.priority === 'urgent' ? 'urgent' : '') + '">' +
        '<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">' +
        '<span class="badge badge-' + a.priority + '">' + a.priority + '</span>' +
        '<div class="announce-title">' + a.title + '</div>' +
        '</div>' +
        '<div class="announce-body">' + a.body.slice(0, 90) + '...</div>' +
        '<div class="announce-meta">🕐 ' + a.date + '</div>' +
        '</div>';
    }).join('');
  }

  setTimeout(function () {
    drawDonut(pending, done, overdue);
    drawWeeklyBar(today);
  }, 100);
}

function drawDonut(pending, done, overdue) {
  if (typeof Chart === 'undefined') return;
  var ctx = document.getElementById('taskDonut');
  if (!ctx) return;
  if (donutChart) donutChart.destroy();
  donutChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Pending', 'Completed', 'Overdue'],
      datasets: [{ data: [pending, done, overdue], backgroundColor: ['#f59e0b', '#10b981', '#ef4444'], borderWidth: 0, hoverOffset: 6 }]
    },
    options: { responsive: true, cutout: '68%', plugins: { legend: { position: 'bottom', labels: { font: { family: 'Nunito', size: 12 }, padding: 16 } } } }
  });
}

function drawWeeklyBar(today) {
  if (typeof Chart === 'undefined') return;
  var ctx = document.getElementById('weeklyBar');
  if (!ctx) return;
  if (barChart) barChart.destroy();
  var labels = [], counts = [];
  for (var i = 0; i < 7; i++) {
    var d = new Date(today); d.setDate(d.getDate() + i);
    labels.push(d.toLocaleDateString('en-GB', { weekday: 'short' }));
    var ds = d.toISOString().split('T')[0];
    counts.push(tasks.filter(function (t) { return t.due === ds; }).length);
  }
  barChart = new Chart(ctx, {
    type: 'bar',
    data: { labels: labels, datasets: [{ label: 'Tasks Due', data: counts, backgroundColor: '#4f8ef7', borderRadius: 6 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } } }
  });
}

function taskItemHTML(t) {
  var today = new Date(); today.setHours(0, 0, 0, 0);
  var overdue = !t.done && t.due && new Date(t.due) < today;
  var progClass = t.progress < 35 ? 'prog-low' : t.progress < 70 ? 'prog-mid' : 'prog-high';
  var sub = subjects.find(function (s) { return s.code === t.subject; });
  return '<div class="task-item ' + (t.done ? 'done' : '') + '" id="task-' + t.id + '">' +
    '<div class="task-check ' + (t.done ? 'checked' : '') + '" onclick="toggleTask(' + t.id + ')">' + (t.done ? '✓' : '') + '</div>' +
    '<div class="task-content">' +
    '<div class="task-title">' + t.title + '</div>' +
    (t.desc ? '<div style="font-size:12px;color:var(--text-muted);margin:3px 0">' + t.desc + '</div>' : '') +
    '<div class="task-meta">' +
    (sub ? '<span class="badge badge-subject">' + sub.icon + ' ' + sub.name + '</span>' : '') +
    '<span class="badge badge-' + t.priority + '">' + t.priority + '</span>' +
    '<span class="badge badge-type">' + t.type + '</span>' +
    (t.due ? '<span style="font-size:12px;' + (overdue ? 'color:var(--danger);font-weight:700' : '') + '">' + (overdue ? '⚠️ Overdue' : '📅') + ' ' + fmtDate(t.due) + '</span>' : '') +
    '</div>' +
    '<div class="progress-wrap">' +
    '<div class="progress-label"><span>Progress</span><span>' + t.progress + '%</span></div>' +
    '<div class="progress-bar-outer"><div class="progress-fill ' + progClass + '" style="width:' + t.progress + '%"></div></div>' +
    '</div>' +
    '</div>' +
    '<div style="display:flex;gap:6px;flex-shrink:0;">' +
    '<button class="btn-sf btn-ghost-sf btn-sm-sf" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="openEditTask(' + t.id + ')">✏️</button>' +
    '<button class="btn-sf btn-danger-sf btn-sm-sf" onclick="deleteTask(' + t.id + ')">🗑</button>' +
    '</div>' +
    '</div>';
}

function renderTaskList() {
  var search = (getVal('task-search') || '').toLowerCase();
  var fs = getVal('f-status') || '';
  var fp = getVal('f-priority') || '';

  var filtered = tasks.filter(function (t) {
    if (search && !t.title.toLowerCase().includes(search)) return false;
    if (fs === 'pending' && t.done) return false;
    if (fs === 'done' && !t.done) return false;
    if (fp && t.priority !== fp) return false;
    return true;
  });

  var el = document.getElementById('task-list-container');
  if (!el) return;
  el.innerHTML = filtered.length
    ? filtered.map(taskItemHTML).join('')
    : '<div class="empty-state card" style="padding:56px;"><div class="empty-icon">📭</div><h3>No tasks found</h3><p>Add a new task or clear filters.</p></div>';
}

function toggleTask(id) {
  var t = tasks.find(function (t) { return t.id === id; });
  if (!t) return;
  t.done = !t.done;
  if (t.done) t.progress = 100;
  renderTaskList();
  renderDashboard();

  if (typeof Swal !== 'undefined') {
    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true })
      .fire({ icon: t.done ? 'success' : 'info', title: t.done ? 'Task completed! 🎉' : 'Marked as pending' });
  }
}

function openNewTask(type) {
  Swal.fire({
    icon: 'error',
    title: 'Access Denied',
    text: 'You cannot add tasks or assignments. Please contact your administrator.',
  });

}

function openEditTask(id) {
  var t = tasks.find(function (t) { return t.id === id; });
  if (!t) return;
  editingId = id;
  setText('modal-title', '✏️ Edit Task');
  setVal('m-title', t.title);
  setVal('m-desc', t.desc || '');
  setVal('m-subject', t.subject || '');
  setVal('m-type', t.type);
  setVal('m-priority', t.priority);
  setVal('m-due', t.due || '');
  setVal('m-progress', t.progress || 0);
  setText('prog-display', (t.progress || 0) + '%');
}

function saveTask() {
  Swal.fire({
    icon: 'error',
    title: 'Access Denied',
    text: 'You cannot add or edit tasks/assignments. Please contact your administrator.',
  });

}

function deleteTask(id) {
  Swal.fire({
    icon: 'error',
    title: 'Access Denied',
    text: 'You cannot delete tasks or assignments. Please contact your administrator.',
  });

}

function renderAssignments() {
  var assigns = tasks.filter(function (t) { return t.type === 'assignment'; });
  var today = new Date(); today.setHours(0, 0, 0, 0);
  var tbody = document.getElementById('assign-tbody');
  if (!tbody) return;
  tbody.innerHTML = assigns.length ? assigns.map(function (t) {
    var sub = subjects.find(function (s) { return s.code === t.subject; });
    var overdue = !t.done && t.due && new Date(t.due) < today;
    var progClass = t.progress < 35 ? 'prog-low' : t.progress < 70 ? 'prog-mid' : 'prog-high';
    return '<tr>' +
      '<td style="font-weight:700;">' + t.title + '</td>' +
      '<td>' + (sub ? '<span class="badge badge-subject">' + sub.icon + ' ' + sub.name + '</span>' : '—') + '</td>' +
      '<td style="' + (overdue ? 'color:var(--danger);font-weight:700' : 'color:var(--text-muted)') + '">' + (t.due ? fmtDate(t.due) : '—') + '</td>' +
      '<td><span class="badge badge-' + t.priority + '">' + t.priority + '</span></td>' +
      '<td style="min-width:130px;"><div class="progress-bar-outer"><div class="progress-fill ' + progClass + '" style="width:' + t.progress + '%"></div></div><span style="font-size:11px;color:var(--text-muted);">' + t.progress + '%</span></td>' +
      '<td><span class="badge ' + (t.done ? 'badge-done' : 'badge-pending') + '">' + (t.done ? '✅ Done' : '⏳ Pending') + '</span></td>' +
      '<td>' +
      '<button class="btn-sf btn-ghost-sf btn-sm-sf" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="openEditTask(' + t.id + ')" style="margin-right:4px;">✏️</button>' +
      '<button class="btn-sf btn-danger-sf btn-sm-sf" onclick="deleteTask(' + t.id + ')">🗑</button>' +
      '</td>' +
      '</tr>';
  }).join('')
    : '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">No assignments yet. Add a task with type "Assignment".</td></tr>';
}

function renderSubjects() {
  var el = document.getElementById('subject-grid');
  if (!el) return;
  el.innerHTML = subjects.map(function (s) {
    var total = tasks.filter(function (t) { return t.subject === s.code; }).length;
    var done = tasks.filter(function (t) { return t.subject === s.code && t.done; }).length;
    var pct = total ? Math.round(done / total * 100) : 0;
    var progClass = pct < 35 ? 'prog-low' : pct < 70 ? 'prog-mid' : 'prog-high';
    return '<div style="background:#fff;border-radius:var(--radius);box-shadow:var(--shadow);padding:24px;border-top:4px solid ' + s.color + ';transition:transform .2s;" onmouseover="this.style.transform=\'translateY(-3px)\'" onmouseout="this.style.transform=\'\'">' +
      '<div style="font-size:32px;margin-bottom:10px;">' + s.icon + '</div>' +
      '<div style="font-size:16px;font-weight:800;color:' + s.color + ';margin-bottom:3px;">' + s.name + '</div>' +
      '<div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">' + s.code + '</div>' +
      '<div style="font-size:12px;color:var(--text-muted);margin-bottom:8px;">' + total + ' tasks · ' + done + ' done</div>' +
      '<div class="progress-bar-outer"><div class="progress-fill ' + progClass + '" style="width:' + pct + '%"></div></div>' +
      '<div style="font-size:11px;color:var(--text-muted);margin-top:4px;">' + pct + '% complete</div>' +
      '</div>';
  }).join('');
}

function renderAnnouncements() {
  var el = document.getElementById('announce-full');
  if (!el) return;
  el.innerHTML = announcements.map(function (a) {
    return '<div class="announce-card announce-card-student ' + (a.priority === 'urgent' ? 'urgent' : '') + '" style="margin-bottom:16px;">' +
      '<div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">' +
      '<span class="badge badge-' + a.priority + '">' + (a.priority === 'urgent' ? '🚨' : '🔵') + ' ' + a.priority + '</span>' +
      '<div class="announce-title" style="font-size:17px;">' + a.title + '</div>' +
      '</div>' +
      '<div class="announce-body" style="font-size:14px;line-height:1.7;">' + a.body + '</div>' +
      '<div class="announce-meta" style="margin-top:12px;">📌 Posted by Admin · ' + a.date + '</div>' +
      '</div>';
  }).join('');
}

function setText(id, val) {
  var el = document.getElementById(id);
  if (el) el.textContent = val;
}
function setVal(id, val) {
  var el = document.getElementById(id);
  if (el) el.value = val;
}
function getVal(id) {
  var el = document.getElementById(id);
  return el ? el.value : '';
}
function fmtDate(str) {
  return new Date(str + 'T12:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

document.addEventListener('DOMContentLoaded', function () {

  var dateEl = document.getElementById('today-date');
  if (dateEl) dateEl.textContent = new Date().toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });


  if (typeof flatpickr !== 'undefined') {
    flatpickr('.datepicker', { dateFormat: 'Y-m-d', allowInput: true });
  }


  var toggle = document.getElementById('menu-toggle');
  function checkMenuToggle() {
    if (toggle) toggle.style.display = window.innerWidth <= 900 ? 'flex' : 'none';
  }
  checkMenuToggle();
  window.addEventListener('resize', checkMenuToggle);

  renderDashboard();
});
