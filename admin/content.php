<?php
require_once __DIR__ . '/auth-check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-helper.php';

$pageTitle = 'Manage Content';

// Handle batch content update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_all') {
    try {
        $db = getDB();
        $db->beginTransaction();
        
        $contentKeys = ['hero_title', 'hero_description', 'contact_email', 'contact_phone', 'about_text'];
        
        foreach ($contentKeys as $key) {
            $value = $_POST[$key] ?? '';
            $stmt = $db->prepare("
                INSERT INTO SiteContents (Key, Value, UpdatedAt)
                VALUES (?, ?, datetime('now'))
            ");
            try {
                $stmt->execute([$key, $value]);
            } catch (PDOException $e) {
                // If insert fails, try update
                $stmt = $db->prepare("UPDATE SiteContents SET Value = ?, UpdatedAt = datetime('now') WHERE Key = ?");
                $stmt->execute([$value, $key]);
            }
        }
        
        $db->commit();
        $message = 'Content updated successfully!';
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $error = 'Failed to update content: ' . $e->getMessage();
        error_log("Content update error: " . $e->getMessage());
    }
}

// Load content
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM SiteContents");
    $contents = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $contents[$row['Key']] = $row['Value'];
    }
} catch (PDOException $e) {
    error_log("Error loading content: " . $e->getMessage());
    $contents = [];
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="min-h-screen bg-background">
    <div class="container mx-auto px-4 py-8">
        <?php if (isset($message)): ?>
            <div class="mb-4 p-3 bg-green-100 border border-green-400 rounded text-green-700">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 rounded text-red-700">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-foreground">Manage Content</h1>
            <button
                type="submit"
                form="content-form"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded hover:opacity-90 transition disabled:opacity-50"
            >
                <iconify-icon icon="mdi:content-save" width="20" height="20"></iconify-icon>
                Save All
            </button>
        </div>

        <form method="POST" id="content-form" class="space-y-6 max-w-4xl">
            <input type="hidden" name="action" value="save_all">
            
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Hero Title</label>
                <input
                    type="text"
                    name="hero_title"
                    value="<?php echo htmlspecialchars($contents['hero_title'] ?? ''); ?>"
                    class="w-full px-4 py-2 border border-border rounded bg-background text-foreground"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Hero Description</label>
                <textarea
                    name="hero_description"
                    rows="3"
                    class="w-full px-4 py-2 border border-border rounded bg-background text-foreground"
                ><?php echo htmlspecialchars($contents['hero_description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Contact Email</label>
                <input
                    type="email"
                    name="contact_email"
                    value="<?php echo htmlspecialchars($contents['contact_email'] ?? ''); ?>"
                    class="w-full px-4 py-2 border border-border rounded bg-background text-foreground"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Contact Phone</label>
                <input
                    type="tel"
                    name="contact_phone"
                    value="<?php echo htmlspecialchars($contents['contact_phone'] ?? ''); ?>"
                    class="w-full px-4 py-2 border border-border rounded bg-background text-foreground"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-foreground mb-2">About Text</label>
                <textarea
                    name="about_text"
                    rows="5"
                    class="w-full px-4 py-2 border border-border rounded bg-background text-foreground"
                ><?php echo htmlspecialchars($contents['about_text'] ?? ''); ?></textarea>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

