<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.html');
  exit;
}
$adminName = $_SESSION['name'] ?? 'Admin';
$students = [];


$assignments = [];
try {
  $stmt = $pdo->query("SELECT * FROM assignments ORDER BY due_date ASC");
  $assignments = $stmt->fetchAll();
} catch (Exception $e) {
  $assignments = [];
}

?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
  <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;">
    Assignments <span style="color:var(--purple);">Management</span>
  </div>
  <button class="btn-sf btn-purple btn-sm-sf" data-bs-toggle="modal" data-bs-target="#assignmentModal">+ Add New Assignment</button>
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
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="admin-assignments-tbody">
                <?php if (count($assignments) > 0): ?>
                  <?php foreach ($assignments as $a): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($a['title']); ?></td>
                      <td><?php echo htmlspecialchars($a['subject']); ?></td>
                      <td><?php echo htmlspecialchars($a['due_date']); ?></td>
                      <td><?php echo htmlspecialchars($a['priority']); ?></td>
                      <td>Pending</td>
                      <td>
                        <button class="btn-sf btn-danger btn-sm-sf" onclick="deleteAssignment('<?php echo $a['id']; ?>')">Delete</button>
                        <button class="btn-sf btn-success btn-sm-sf" onclick="confirmAssignment('<?php echo $a['id']; ?>')">Confirm</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" style="text-align:center;color:#888;padding:24px;">No assignments found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
    \
        <div class="modal fade" id="assignmentModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="save_assignment.php" method="POST">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Assignment</h5>
                  <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="form-group-sf">
                    <label>Title *</label>
                    <input type="text" name="title" placeholder="Assignment title" required>
                  </div>
                  <div class="form-group-sf">
                    <label>Subject *</label>
                    <input type="text" name="subject" placeholder="Subject" required>
                  </div>
                  <div class="form-group-sf">
                    <label>Due Date *</label>
                    <input type="date" name="due" required>
                  </div>
                  <div class="form-group-sf">
                    <label>Priority</label>
                    <select name="priority">
                      <option value="normal">🔵 Normal</option>
                      <option value="urgent">🔴 Urgent</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn-sf btn-ghost-sf" data-bs-dismiss="modal" type="button">Cancel</button>
                  <button class="btn-sf btn-purple" type="submit">Add Assignment</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <script>
          function addAssignment() {
            
            Swal.fire({ icon:'success', title:'Assignment added!' });
          
            var modal = bootstrap.Modal.getInstance(document.getElementById('assignmentModal'));
            if (modal) modal.hide();
          }
          function deleteAssignment(id) {
            Swal.fire({
              title:'Delete Assignment?', text:'This cannot be undone.', icon:'warning',
              showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
              confirmButtonText:'🗑 Yes, delete', cancelButtonText:'Cancel'
            }).then(function(result){
              if (result.isConfirmed) {
        
                Swal.fire({ icon:'success', title:'Deleted!', timer:1500, showConfirmButton:false });
              }
            });
          }
          function confirmAssignment(id) {
            Swal.fire({ icon:'success', title:'Assignment confirmed!' });
         
          }
        </script>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — StudyFlow</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

  <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<div class="layout">

  <aside class="sidebar sidebar-admin" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <div class="logo-icon logo-icon-admin">⚙️</div>
        <div class="logo-text">
          <strong>StudyFlow</strong>
          <span>Admin Panel</span>
        </div>
      </div>
    </div>

    <div class="sidebar-user">
      <div class="s-avatar s-avatar-admin">
        <?php echo strtoupper(substr($adminName, 0, 1)); ?>
      </div>
      <div>
        <div class="user-name"><?php echo htmlspecialchars($adminName); ?></div>
        <div class="user-role role-admin">Administrator</div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Overview</div>
      <div class="nav-link active-admin" onclick="showPage('dashboard',this)">
        <span class="nav-icon">📊</span> Dashboard
      </div>
      <div class="nav-section-label">Manage</div>
      <div class="nav-link" onclick="showPage('students',this)">
        <span class="nav-icon">👥</span> Students
      </div>
      <div class="nav-link" onclick="showPage('tasks',this)">
        <span class="nav-icon">✅</span> All Tasks
      </div>
      <div class="nav-link" onclick="showPage('assignments',this)">
        <span class="nav-icon">📋</span> Assignments
      </div>
      <div class="nav-link" onclick="showPage('admin-tasks',this)">
        <span class="nav-icon">📝</span> Task Management
      </div>
      <div class="nav-link" onclick="showPage('announcements',this)">
        <span class="nav-icon">📢</span> Announcements
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
        <div class="topbar-title" id="topbar-title" style="color:var(--purple);">
          Admin <span style="color:var(--text);">Dashboard</span>
        </div>
      </div>
      <div class="topbar-right">
        <div class="topbar-date">📅 <span id="today-date"></span></div>
        <button class="btn-sf btn-purple btn-sm-sf"
                data-bs-toggle="modal" data-bs-target="#annModal">
          📢 New Announcement
        </button>
      </div>
    </div>

    <div class="page-body">

      <div id="page-dashboard" class="page-section active">
        <div class="hero-banner hero-banner-admin">
          <h2>Admin Control Panel ⚙️</h2>
          <p>Manage students, tasks, subjects, and announcements from here.</p>
        </div>

        <div class="stats-grid">
          <div class="stat-card blue">
            <div class="stat-icon">👥</div>
            <div><div class="stat-num" id="s-students">0</div><div class="stat-label">Students</div></div>
          </div>
          <div class="stat-card yellow">
            <div class="stat-icon">📝</div>
            <div><div class="stat-num" id="s-tasks">0</div><div class="stat-label">Total Tasks</div></div>
          </div>
          <div class="stat-card green">
            <div class="stat-icon">✅</div>
            <div><div class="stat-num" id="s-done">0</div><div class="stat-label">Tasks Done</div></div>
          </div>
          <div class="stat-card purple">
            <div class="stat-icon">📢</div>
            <div><div class="stat-num" id="s-ann">0</div><div class="stat-label">Announcements</div></div>
          </div>
        </div>

        <div class="grid-2">
          <div class="card">
            <div class="card-header">
              <div class="card-title">👥 Recent Students</div>
              <span onclick="showPage('students',document.querySelectorAll('.nav-link')[1])"
                    style="font-size:12px;color:var(--primary);font-weight:600;cursor:pointer;">View all</span>
            </div>
            <div class="card-body" style="padding-top:0;overflow-x:auto;">
              <table class="sf-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Tasks</th>
                  </tr>
                </thead>
                <tbody id="recent-students"></tbody>
              </table>
            </div>
          </div>
          <div class="card">
            <div class="card-header">
              <div class="card-title">📋 Recent Tasks</div>
              <span onclick="showPage('tasks',document.querySelectorAll('.nav-link')[2])"
                    style="font-size:12px;color:var(--primary);font-weight:600;cursor:pointer;">View all</span>
            </div>
            <div class="card-body" style="padding-top:0;overflow-x:auto;">
              <table class="sf-table">
                <thead>
                  <tr>
                    <th>Task</th>
                    <th>Student</th>
                    <th>Priority</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="recent-tasks"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div id="page-students" class="page-section">
        <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin-bottom:22px;">
          👥 All <span style="color:var(--purple);">Students</span>
        </div>
        <div class="card">
          <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="sf-table">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Student ID</th>
                  <th>Course</th>
                  <th style="text-align:center;">Tasks</th>
                  <th>Completion</th>
                </tr>
              </thead>
              <tbody id="all-students">
                <?php if (count($students) > 0): ?>
                  <?php foreach ($students as $student): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($student['name']); ?></td>
                      <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                      <td><?php echo htmlspecialchars($student['course']); ?></td>
                      <td style="text-align:center;">--</td>
                      <td>--</td>
                      <td>
                        <button class="btn-sf btn-danger btn-sm-sf" onclick="deleteStudent('<?php echo $student['student_id']; ?>')">Delete</button>
                        <button class="btn-sf btn-success btn-sm-sf" onclick="acceptStudent('<?php echo $student['student_id']; ?>')">Accept</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" style="text-align:center;color:#888;padding:24px;">No students found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="page-tasks" class="page-section">
        <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;margin-bottom:22px;">
          ✅ All <span style="color:var(--purple);">Tasks</span>
        </div>
        <div class="card">
          <div class="card-body" style="padding:0;overflow-x:auto;">
            <table class="sf-table">
              <thead>
                <tr>
                  <th>Task</th>
                  <th>Student</th>
                  <th>Subject</th>
                  <th>Priority</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="all-tasks-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div id="page-announcements" class="page-section">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:22px;">
          <div style="font-family:'Poppins',sans-serif;font-size:20px;font-weight:700;">
            📢 <span style="color:var(--purple);">Announcements</span>
          </div>
          <button class="btn-sf btn-purple btn-sm-sf"
                  data-bs-toggle="modal" data-bs-target="#annModal">+ New</button>
        </div>
        <div id="ann-list"></div>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="annModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📢 New Announcement</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="form-group-sf">
          <label>Title *</label>
          <input type="text" id="ann-title" placeholder="Announcement title">
        </div>
        <div class="form-group-sf">
          <label>Message *</label>
          <textarea id="ann-body" placeholder="Write your announcement..." style="min-height:100px;"></textarea>
        </div>
        <div class="form-group-sf">
          <label>Priority</label>
          <select id="ann-priority">
            <option value="normal">🔵 Normal</option>
            <option value="urgent">🔴 Urgent</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-sf btn-ghost-sf" data-bs-dismiss="modal">Cancel</button>
        <button class="btn-sf btn-purple" onclick="postAnnouncement()">📢 Post Announcement</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
  window.SF_STUDENTS      = <?php echo json_encode($students,      JSON_UNESCAPED_UNICODE); ?>;
  window.SF_ALL_TASKS     = <?php echo json_encode($allTasks,      JSON_UNESCAPED_UNICODE); ?>;
  window.SF_ANNOUNCEMENTS = <?php echo json_encode($announcements, JSON_UNESCAPED_UNICODE); ?>;
  window.SF_ADMIN_NAME    = <?php echo json_encode($adminName,     JSON_UNESCAPED_UNICODE); ?>;
</script>

<script src="../js/admin-dashboard.js"></script>
<script>
function deleteStudent(studentId) {
  Swal.fire({
    title:'Delete Student?', text:'This cannot be undone.', icon:'warning',
    showCancelButton:true, confirmButtonColor:'#ef4444', cancelButtonColor:'#6b7280',
    confirmButtonText:'🗑 Yes, delete', cancelButtonText:'Cancel'
  }).then(function(result){
    if (result.isConfirmed) {
    
      Swal.fire({ icon:'success', title:'Deleted!', timer:1500, showConfirmButton:false });
    }
  });
}
function acceptStudent(studentId) {
  Swal.fire({ icon:'success', title:'Student accepted!' });
 
}
</script>

</body>
</html>
