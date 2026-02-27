<?php

require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../login.html');
    exit;
}

$userId   = $_SESSION['user_id'];
$userName = $_SESSION['name'] ?? 'Student';

$tasks         = [];
$announcements = [];
$subjects      = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — StudyFlow Student</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">


  <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

  
  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<div class="layout">

  <aside class="sidebar sidebar-student" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-icon logo-icon-student">🎓</div>
        <div class="logo-text">
          <strong>StudyFlow</strong>
          <span>Student Portal</span>
        </div>
      </div>
    </div>

    <div class="sidebar-user">
      <div class="s-avatar s-avatar-student">
        <?php echo strtoupper(substr($userName, 0, 1)); ?>
      </div>
      <div>
        <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
        <div class="user-role role-student">Student</div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Main</div>
      <div class="nav-link active-student" onclick="showPage('dashboard',this)">
        <span class="nav-icon">⚡</span> Dashboard
      </div>
      <div class="nav-link" onclick="showPage('tasks',this)">
        <span class="nav-icon">✅</span> My Tasks
      </div>
      <div class="nav-link" onclick="showPage('assignments',this)">
        <span class="nav-icon">📋</span> Assignments
      </div>
      <div class="nav-section-label">Tools</div>
      <div class="nav-link" onclick="showPage('subjects',this)">
        <span class="nav-icon">📚</span> Subjects
      </div>
      <div class="nav-link" onclick="showPage('announcements',this)">
        <span class="nav-icon">📢</span> Announcements
      </div>
      <div class="nav-section-label">Account</div>
      <div class="nav-link" onclick="showPage('profile',this)">
        <span class="nav-icon">👤</span> My Profile
      </div>
    </nav>

    <div class="sidebar-footer">
      <a href="../logout.php" class="nav-link" style="color:rgba(255,100,100,.7);">
        <span class="nav-icon">🚪</span> Logout
      </a>
    </div>
  </aside>


  <div class="main-content">

    <div class="topbar">
      <div style="display:flex;align-items:center;gap:12px;">
        <button id="menu-toggle"
                onclick="document.getElementById('sidebar').classList.toggle('open')"
                class="menu-toggle-btn"
                style="background:none;border:2px solid var(--border);border-radius:8px;padding:8px 12px;cursor:pointer;font-size:16px;display:none;">
          ☰
        </button>
        <div class="topbar-title" id="topbar-title">
          Dashboard <span style="color:var(--primary)">Overview</span>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-date">📅 <span id="today-date"></span></div>
      </div>
    </div>

    <div class="page-body">

      <div id="page-dashboard" class="page-section active">
        <div class="hero-banner hero-banner-student" style="--emoji:'🎓'">
          <h2 id="greeting">Good Morning, <?php echo htmlspecialchars($userName); ?>! 👋</h2>
          <p>You have <strong id="stat-pending-hero">0</strong> pending tasks. Keep going!</p>
        </div>

        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-icon">📝</div>
            <div><div class="stat-num" id="stat-total">0</div><div class="stat-label">Total Tasks</div></div>
          </div>
          <div class="stat-card yellow">
            <div class="stat-icon">⏳</div>
            <div><div class="stat-num" id="stat-pending">0</div><div class="stat-label">Pending</div></div>
          </div>
          <div class="stat-card green">
            <div class="stat-icon">✅</div>
            <div><div class="stat-num" id="stat-done">0</div><div class="stat-label">Completed</div></div>
          </div>
          <div class="stat-card red">
            <div class="stat-icon">⚠️</div>
            <div><div class="stat-num" id="stat-overdue">0</div><div class="stat-label">Overdue</div></div>
          </div>
        </div>

        <div class="grid-2">
      
          <div class="card">
            <div class="card-header"><div class="card-title">📊 Task Status Overview</div></div>
            <div class="card-body" style="text-align:center;">
              <canvas id="taskDonut" style="max-height:220px;"></canvas>
            </div>
          </div>
        
          <div class="card">
            <div class="card-header">
              <div class="card-title">📢 Announcements</div>
              <span onclick="showPage('announcements',document.querySelectorAll('.nav-link')[4])"
                    style="font-size:12px;color:var(--primary);font-weight:600;cursor:pointer;">View all</span>
            </div>
            <div class="card-body" id="announce-preview"></div>
          </div>
        </div>

        <div class="card" style="margin-bottom:24px;">
          <div class="card-header"><div class="card-title">📅 Tasks Due This Week</div></div>
          <div class="card-body">
            <canvas id="weeklyBar" style="max-height:180px;"></canvas>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <div class="card-title">🔥 Due in Next 7 Days</div>
            <span onclick="showPage('tasks',document.querySelectorAll('.nav-link')[1])"
                  style="font-size:12px;color:var(--primary);font-weight:600;cursor:pointer;">View all</span>
          </div>
          <div class="card-body" id="due-soon-list"></div>
        </div>
      </div>

      <div id="page-tasks" class="page-section">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
          <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;">
            My <span style="color:var(--primary);">Tasks</span>
          </div>
        </div>
        <div class="filter-bar">
          <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="task-search" placeholder="Search tasks..."
                   oninput="renderTaskList()"
                   style="padding:11px 14px 11px 40px;border:2px solid var(--border);border-radius:var(--radius-sm);font-family:'Nunito',sans-serif;font-size:13px;outline:none;width:100%;">
          </div>
          <select id="f-status" onchange="renderTaskList()"
                  style="padding:11px 14px;border:2px solid var(--border);border-radius:var(--radius-sm);font-family:'Nunito',sans-serif;font-size:13px;outline:none;">
            <option value="">All Status</option>
            <option value="pending">⏳ Pending</option>
            <option value="done">✅ Done</option>
          </select>
          <select id="f-priority" onchange="renderTaskList()"
                  style="padding:11px 14px;border:2px solid var(--border);border-radius:var(--radius-sm);font-family:'Nunito',sans-serif;font-size:13px;outline:none;">
            <option value="">All Priority</option>
            <option value="high">🔴 High</option>
            <option value="medium">🟡 Medium</option>
            <option value="low">🟢 Low</option>
          </select>
        </div>
        <div id="task-list-container"></div>
      </div>

      <div id="page-assignments" class="page-section">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
          <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;">
            Assignments <span style="color:var(--primary);">Tracker</span>
          </div>
        </div>
        <div class="card">
          <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="sf-table">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Subject</th>
                  <th>Due Date</th>
                  <th>Priority</th>
                  <th>Progress</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="assign-tbody"></tbody>
            </table>
          </div>
        </div>
        <script>
          function adminOnlyError() {
            Swal.fire({
              icon: 'error',
              title: 'Access Denied',
              text: 'You do not have permission to perform this action. Please contact your administrator.',
            });
          }
        
          document.querySelectorAll('.btn-admin-only').forEach(btn => {
            btn.addEventListener('click', adminOnlyError);
          });
        </script>
      </div>

      <div id="page-subjects" class="page-section">
        <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin-bottom:22px;">
          My <span style="color:var(--primary);">Subjects</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px;"
             id="subject-grid"></div>
      </div>

      <div id="page-announcements" class="page-section">
        <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin-bottom:22px;">
          📢 <span style="color:var(--primary);">Announcements</span>
        </div>
        <div id="announce-full"></div>
      </div>

      <div id="page-profile" class="page-section">
        <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin-bottom:22px;">
          👤 My <span style="color:var(--primary);">Profile</span>
        </div>
        <div class="card" style="max-width:480px;">
          <div class="card-body">
            <div style="text-align:center;margin-bottom:28px;">
              <div style="width:80px;height:80px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:#fff;margin:0 auto 12px;">
                <?php echo strtoupper(substr($userName, 0, 1)); ?>
              </div>
              <div style="font-size:18px;font-weight:700;"><?php echo htmlspecialchars($userName); ?></div>
              <div style="font-size:13px;color:var(--text-muted);"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></div>
            </div>
           
            <form action="../update_profile.php" method="POST">
              <div class="form-group-sf">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($userName); ?>">
              </div>
              <div class="form-group-sf">
                <label>Student ID</label>
                <input type="text" name="student_id" value="<?php echo htmlspecialchars($_SESSION['student_id'] ?? ''); ?>">
              </div>
              <div class="form-group-sf">
                <label>Course</label>
                <input type="text" name="course" value="<?php echo htmlspecialchars($_SESSION['course'] ?? ''); ?>">
              </div>
              <div class="form-group-sf">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
              </div>
              <button type="submit" class="btn-sf btn-primary-sf">💾 Save Changes</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="taskModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-title">Add New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="m-id">
        <div class="form-group-sf">
          <label>Task Title *</label>
          <input type="text" id="m-title" placeholder="e.g. Complete SEN4002 Report">
        </div>
        <div class="form-group-sf">
          <label>Description</label>
          <textarea id="m-desc" placeholder="Task details..."></textarea>
        </div>
        <div class="form-row-sf">
          <div class="form-group-sf">
            <label>Subject</label>
            <select id="m-subject">
              <option value="">— None —</option>
              <option value="SEN4002">💻 Software Design &amp; Dev</option>
              <option value="DBS3001">🗄️ Database Systems</option>
              <option value="WEB2001">🌐 Web Development</option>
              <option value="MAT1001">📐 Mathematics</option>
            </select>
          </div>
          <div class="form-group-sf">
            <label>Type</label>
            <select id="m-type">
              <option value="task">📝 Task</option>
              <option value="assignment">📋 Assignment</option>
              <option value="exam">📖 Exam</option>
              <option value="lab">🔬 Lab Work</option>
              <option value="project">🚀 Project</option>
            </select>
          </div>
        </div>
        <div class="form-row-sf">
          <div class="form-group-sf">
            <label>Priority</label>
            <select id="m-priority">
              <option value="high">🔴 High</option>
              <option value="medium" selected>🟡 Medium</option>
              <option value="low">🟢 Low</option>
            </select>
          </div>
          <div class="form-group-sf">
            <label>Due Date</label>
            <input type="text" id="m-due" class="datepicker" placeholder="Pick a date...">
          </div>
        </div>
        <div class="form-group-sf">
          <label>Progress: <strong id="prog-display">0%</strong></label>
          <input type="range" id="m-progress" min="0" max="100" value="0"
                 style="width:100%;accent-color:var(--primary);"
                 oninput="document.getElementById('prog-display').textContent=this.value+'%'">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-sf btn-ghost-sf" data-bs-dismiss="modal">Cancel</button>
        <button class="btn-sf btn-primary-sf" onclick="saveTask()">💾 Save Task</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>

  window.SF_TASKS         = <?php echo json_encode($tasks,         JSON_UNESCAPED_UNICODE); ?>;
  window.SF_ANNOUNCEMENTS = <?php echo json_encode($announcements, JSON_UNESCAPED_UNICODE); ?>;
  window.SF_SUBJECTS      = <?php echo json_encode($subjects,      JSON_UNESCAPED_UNICODE); ?>;
  window.SF_USER_NAME     = <?php echo json_encode($userName,      JSON_UNESCAPED_UNICODE); ?>;
</script>


<script src="../js/student-dashboard.js"></script>

</body>
</html>
