<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['admin_id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

if (!empty($_SESSION['user_id'])) {
    header('Location: universities.php');
    exit;
}

$errors = [];
$username = '';

function sanitize_redirect_target($target)
{
    $target = trim($target);
    if ($target === '') {
        return 'universities.php';
    }

    if (strpos($target, '://') !== false || strpos($target, '../') !== false) {
        return 'universities.php';
    }

    return ltrim($target, '/');
}

$redirectTarget = sanitize_redirect_target($_POST['redirect'] ?? $_GET['redirect'] ?? 'universities.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Enter both username and password.';
    } else {
        $loggedIn = false;

        $adminStmt = $conn->prepare('SELECT id, password_hash, fullname FROM admin_users WHERE username = ? LIMIT 1');
        if ($adminStmt) {
            $adminStmt->bind_param('s', $username);
            $adminStmt->execute();
            $adminResult = $adminStmt->get_result();
            if ($adminResult && ($adminRow = $adminResult->fetch_assoc())) {
                if (password_verify($password, $adminRow['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = $adminRow['id'];
                    $_SESSION['admin_name'] = $adminRow['fullname'];
                    $loggedIn = true;
                }
            }
            $adminStmt->close();
        }

        if ($loggedIn) {
            header('Location: admin/dashboard.php');
            exit;
        }

        $userStmt = $conn->prepare('SELECT id, fullname, password_hash FROM users WHERE username = ? LIMIT 1');
        if ($userStmt) {
            $userStmt->bind_param('s', $username);
            $userStmt->execute();
            $userResult = $userStmt->get_result();
            if ($userResult && ($userRow = $userResult->fetch_assoc())) {
                if (password_verify($password, $userRow['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $userRow['id'];
                    $_SESSION['user_name'] = $userRow['fullname'];
                    header('Location: ' . $redirectTarget);
                    exit;
                }
            }
            $userStmt->close();
        }

        $errors[] = 'Invalid username or password.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="container" style="padding-top: 3rem; padding-bottom: 3rem;">
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <h1>Login</h1>
        <?php if ($errors): ?>
            <?php foreach ($errors as $error): ?>
                <div style="margin-bottom: 0.5rem; padding: 0.6rem 0.9rem; border: 1px solid rgba(239, 68, 68, 0.7); color: #b91c1c; border-radius: 6px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <form method="POST" action="login.php" novalidate>
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTarget, ENT_QUOTES); ?>">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES); ?>" required autofocus>
            <label for="password" style="margin-top: 1rem;">Password</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn" style="margin-top: 1.5rem; width: 100%;">Log in</button>
        </form>
        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">
            Admin credentials take you to the admin console, other usernames land on the public user space (universities list).
        </p>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
