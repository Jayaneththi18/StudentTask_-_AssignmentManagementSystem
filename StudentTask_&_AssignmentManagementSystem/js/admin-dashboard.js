
var students     = window.SF_STUDENTS     || [
  { id:1, name:'Alex Johnson',  email:'alex@student.com',  student_id:'ST12345001', course:'BSc Software Engineering',  tasks:5, done:3 },
  { id:4, name:'Sara Ahmed',    email:'sara@student.com',  student_id:'ST12345004', course:'BSc Software Engineering',  tasks:3, done:1 },
];

var allTasks = window.SF_ALL_TASKS || [
  { title:'SEN4002 PORT1 Report',  student:'Alex Johnson',  subject:'SEN4002', priority:'high',   status:'pending' },
  { title:'ER Diagram Assignment', student:'Priya Patel',   subject:'DBS3001', priority:'medium', status:'pending' },
]

var announcements = window.SF_ANNOUNCEMENTS || [
  { id:1, title:'Semester 2 Timetable Released',  body:'Updated timetable is now available on the portal.',              priority:'urgent', date:'2026-02-20' },
  { id:2, title:'Library Extended Hours',          body:'Library open until 11pm Monday–Friday until end of semester.',  priority:'normal', date:'2026-02-18' },
  { id:3, title:'PORT1 Submission Reminder',       body:'PORT1 (Phase 02) due 28th Feb by 2:00pm via Turnitin.',         priority:'urgent', date:'2026-02-15' },
];

var annNextId = 100;

function showPage(name, btn) {
  document.querySelectorAll('.page-section').forEach(function(p){ p.classList.remove('active'); });
  document.querySelectorAll('.nav-link').forEach(function(b){ b.classList.remove('active-admin'); });

  var section = document.getElementById('page-' + name);
  if (section) section.classList.add('active');
  if (btn) btn.classList.add('active-admin');

  var titles = {
    dashboard:     'Admin <span style="color:var(--text)">Dashboard</span>',
    students:      '👥 All <span style="color:var(--purple)">Students</span>',
    tasks:         '✅ All <span style="color:var(--purple)">Tasks</span>',
    announcements: '📢 <span style="color:var(--purple)">Announcements</span>'
  };
  var titleEl = document.getElementById('topbar-title');
  if (titleEl) titleEl.innerHTML = titles[name] || name;

  if (name === 'dashboard')     renderDashboard();
  if (name === 'students')      renderStudents();
  if (name === 'tasks')         renderAllTasks();
  if (name === 'announcements') renderAnnouncements();
}

function renderDashboard() {
  setText('s-students', students.length);
  setText('s-tasks',    allTasks.length);
  setText('s-done',     allTasks.filter(function(t){ return t.status === 'done'; }).length);
  setText('s-ann',      announcements.length);

  var rs = document.getElementById('recent-students');
  if (rs) {
    rs.innerHTML = students.slice(0,4).map(function(s){
      return '<tr>' +
        '<td style="padding:14px 16px;">' +
          '<div style="display:flex;align-items:center;gap:10px;">' +
            '<div style="width:32px;height:32px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:13px;flex-shrink:0;">' + s.name[0] + '</div>' +
            '<div><div style="font-weight:700;font-size:13px;">' + s.name + '</div><div style="font-size:11px;color:var(--text-muted);">' + s.email + '</div></div>' +
          '</div>' +
        '</td>' +
        '<td style="padding:14px 16px;"><span class="badge badge-subject">' + s.student_id + '</span></td>' +
        '<td style="padding:14px 16px;font-weight:700;">' + s.tasks + '</td>' +
      '</tr>';
    }).join('');
  }

  var rt = document.getElementById('recent-tasks');
  if (rt) {
    rt.innerHTML = allTasks.slice(0,5).map(function(t){
      return '<tr>' +
        '<td style="padding:12px 16px;font-weight:600;font-size:13px;">' + t.title + '</td>' +
        '<td style="padding:12px 16px;font-size:12px;">' + t.student + '</td>' +
        '<td style="padding:12px 16px;"><span class="badge badge-' + t.priority + '">' + t.priority + '</span></td>' +
        '<td style="padding:12px 16px;"><span class="badge badge-' + t.status + '">' + t.status + '</span></td>' +
      '</tr>';
    }).join('');
  }
}

function renderStudents() {
  var el = document.getElementById('all-students');
  if (!el) return;
  el.innerHTML = students.map(function(s){
    var pct = s.tasks ? Math.round(s.done / s.tasks * 100) : 0;
    var pc  = pct < 35 ? 'background:var(--danger)' : pct < 70 ? 'background:var(--warning)' : 'background:var(--success)';
    return '<tr>' +
      '<td style="padding:14px 16px;">' +
        '<div style="display:flex;align-items:center;gap:12px;">' +
          '<div style="width:36px;height:36px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:14px;flex-shrink:0;">' + s.name[0] + '</div>' +
          '<div><div style="font-weight:700;">' + s.name + '</div><div style="font-size:11px;color:var(--text-muted);">' + s.email + '</div></div>' +
        '</div>' +
      '</td>' +
      '<td style="padding:14px 16px;"><span class="badge badge-subject">' + s.student_id + '</span></td>' +
      '<td style="padding:14px 16px;font-size:13px;">' + s.course + '</td>' +
      '<td style="padding:14px 16px;text-align:center;font-weight:700;">' + s.tasks + '</td>' +
      '<td style="padding:14px 16px;min-width:130px;">' +
        '<div class="progress-bar-outer"><div class="progress-fill" style="' + pc + ';width:' + pct + '%"></div></div>' +
        '<div style="font-size:11px;color:var(--text-muted);margin-top:3px;">' + s.done + '/' + s.tasks + ' (' + pct + '%)</div>' +
      '</td>' +
    '</tr>';
  }).join('');
}


function renderAllTasks() {
  var el = document.getElementById('all-tasks-tbody');
  if (!el) return;
  el.innerHTML = allTasks.map(function(t){
    return '<tr>' +
      '<td style="padding:14px 16px;font-weight:700;font-size:13px;">' + t.title + '</td>' +
      '<td style="padding:14px 16px;font-size:13px;">' + t.student + '</td>' +
      '<td style="padding:14px 16px;"><span class="badge badge-subject">' + t.subject + '</span></td>' +
      '<td style="padding:14px 16px;"><span class="badge badge-' + t.priority + '">' + t.priority + '</span></td>' +
      '<td style="padding:14px 16px;"><span class="badge badge-' + t.status + '">' + t.status + '</span></td>' +
    '</tr>';
  }).join('');
}

function renderAnnouncements() {
  var el = document.getElementById('ann-list');
  if (!el) return;
  el.innerHTML = announcements.length ? announcements.map(function(a){
    return '<div class="announce-card announce-card-admin ' + (a.priority === 'urgent' ? 'urgent' : '') + '" style="margin-bottom:14px;">' +
      '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">' +
        '<div style="flex:1;">' +
          '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">' +
            '<span class="badge badge-' + a.priority + '">' + a.priority + '</span>' +
            '<div class="announce-title">' + a.title + '</div>' +
          '</div>' +
          '<div class="announce-body">' + a.body + '</div>' +
          '<div class="announce-meta">📌 Posted by Admin · ' + a.date + '</div>' +
        '</div>' +
        '<button class="btn-sf btn-danger-sf btn-sm-sf" onclick="deleteAnn(' + a.id + ')">🗑 Delete</button>' +
      '</div>' +
    '</div>';
  }).join('')
  : '<div style="text-align:center;padding:40px;color:var(--text-muted);">No announcements yet.</div>';
}

function postAnnouncement() {
  var title = (document.getElementById('ann-title').value || '').trim();
  var body  = (document.getElementById('ann-body').value || '').trim();
  if (!title || !body) { alert('Please fill in title and message.'); return; }

  announcements.unshift({
    id:       ++annNextId,
    title:    title,
    body:     body,
    priority: document.getElementById('ann-priority').value,
    date:     new Date().toISOString().split('T')[0]
  });


  if (typeof bootstrap !== 'undefined') {
    var modal = bootstrap.Modal.getInstance(document.getElementById('annModal'));
    if (modal) modal.hide();
  }

  document.getElementById('ann-title').value = '';
  document.getElementById('ann-body').value  = '';
  renderAnnouncements();
  renderDashboard();

  if (typeof Swal !== 'undefined') {
    Swal.fire({ icon:'success', title:'Announcement posted!', timer:2000, showConfirmButton:false });
  }
}

function deleteAnn(id) {
  if (typeof Swal === 'undefined') {
    if (confirm('Delete this announcement?')) {
      announcements = announcements.filter(function(a){ return a.id !== id; });
      renderAnnouncements(); renderDashboard();
    }
    return;
  }
  Swal.fire({
    title:'Delete Announcement?', icon:'warning',
    showCancelButton:true, confirmButtonColor:'#ef4444',
    confirmButtonText:'🗑 Yes, delete'
  }).then(function(r){
    if (r.isConfirmed) {
      announcements = announcements.filter(function(a){ return a.id !== id; });
      renderAnnouncements(); renderDashboard();
    }
  });
}

function setText(id, val) {
  var el = document.getElementById(id);
  if (el) el.textContent = val;
}

document.addEventListener('DOMContentLoaded', function () {
  var dateEl = document.getElementById('today-date');
  if (dateEl) dateEl.textContent = new Date().toLocaleDateString('en-GB', { weekday:'short', day:'numeric', month:'short', year:'numeric' });

  var toggle = document.getElementById('menu-toggle');
  function checkToggle() {
    if (toggle) toggle.style.display = window.innerWidth <= 900 ? 'flex' : 'none';
  }
  checkToggle();
  window.addEventListener('resize', checkToggle);

  renderDashboard();
});
