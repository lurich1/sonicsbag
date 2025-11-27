<?php
require_once __DIR__ . '/auth-check.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db-helper.php';

$pageTitle = 'Manage Products';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' || $action === 'update') {
        $name = $_POST['name'] ?? '';
        $price = $_POST['price'] ?? '';
        $description = $_POST['description'] ?? '';
        $imageUrl = $_POST['imageUrl'] ?? '';
        $category = $_POST['category'] ?? '';
        $inStock = isset($_POST['inStock']) ? 1 : 0;
        $stockQuantity = intval($_POST['stockQuantity'] ?? 0);
        $tags = $_POST['tags'] ?? [];
        
        // Convert tags array to JSON string
        $tagsJson = json_encode($tags);
        
        try {
            $db = getDB();
            
            if ($action === 'create') {
                $stmt = $db->prepare("
                    INSERT INTO Products (Name, Price, Description, ImageUrl, Category, InStock, StockQuantity, Tags, CreatedAt)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))
                ");
                $stmt->execute([$name, $price, $description, $imageUrl, $category, $inStock, $stockQuantity, $tagsJson]);
                $message = 'Product created successfully!';
            } else {
                $id = intval($_POST['id']);
                $stmt = $db->prepare("
                    UPDATE Products 
                    SET Name = ?, Price = ?, Description = ?, ImageUrl = ?, Category = ?, InStock = ?, StockQuantity = ?, Tags = ?, UpdatedAt = datetime('now')
                    WHERE Id = ?
                ");
                $stmt->execute([$name, $price, $description, $imageUrl, $category, $inStock, $stockQuantity, $tagsJson, $id]);
                $message = 'Product updated successfully!';
            }
        } catch (PDOException $e) {
            $error = 'Failed to save product: ' . $e->getMessage();
            error_log("Product save error: " . $e->getMessage());
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        try {
            $db = getDB();
            $stmt = $db->prepare("DELETE FROM Products WHERE Id = ?");
            $stmt->execute([$id]);
            $message = 'Product deleted successfully!';
        } catch (PDOException $e) {
            $error = 'Failed to delete product: ' . $e->getMessage();
            error_log("Product delete error: " . $e->getMessage());
        }
    }
}

// Load products
$products = getProducts();

$categoryOptions = [
    "Yobo Bag",
    "Homework Book Bag",
    "Business & Branded Bags",
    "Custom Bags",
    "Travel & Duffel Bags",
];

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
            <h1 class="text-3xl font-bold text-foreground">Manage Products</h1>
            <button
                onclick="openProductModal()"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded hover:opacity-90 transition"
            >
                <iconify-icon icon="mdi:plus" width="20" height="20"></iconify-icon>
                Add Product
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-card border border-border rounded-lg overflow-hidden">
                    <div class="relative h-48 bg-muted">
                        <?php if ($product['imageUrl']): ?>
                            <img
                                src="<?php echo getImageUrl($product['imageUrl']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>"
                                class="w-full h-full object-cover"
                            />
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full">
                                <iconify-icon icon="mdi:image" width="48" height="48" class="text-muted-foreground"></iconify-icon>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-foreground mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="text-primary font-semibold mb-2"><?php echo htmlspecialchars($product['price']); ?></p>
                        <div class="flex gap-2 mt-4">
                            <button
                                onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                class="flex-1 px-3 py-2 bg-primary text-primary-foreground rounded text-sm hover:opacity-90 transition"
                            >
                                <iconify-icon icon="mdi:pencil" width="16" height="16" class="inline"></iconify-icon>
                                Edit
                            </button>
                            <button
                                onclick="deleteProduct(<?php echo $product['id']; ?>)"
                                class="flex-1 px-3 py-2 bg-destructive text-destructive-foreground rounded text-sm hover:opacity-90 transition"
                            >
                                <iconify-icon icon="mdi:delete" width="16" height="16" class="inline"></iconify-icon>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div id="product-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-card border border-border rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-foreground mb-4" id="modal-title">Add Product</h2>
            <form id="product-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" id="form-action" value="create">
                <input type="hidden" name="id" id="form-id">
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Name *</label>
                    <input type="text" name="name" id="form-name" required class="w-full px-4 py-2 border border-border rounded bg-background text-foreground" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Price (₵) *</label>
                    <input type="number" name="price" id="form-price" step="0.01" required class="w-full px-4 py-2 border border-border rounded bg-background text-foreground" />
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Description</label>
                    <textarea name="description" id="form-description" rows="3" class="w-full px-4 py-2 border border-border rounded bg-background text-foreground"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-1">Image</label>
                    <input type="file" id="image-upload" accept="image/*" class="w-full px-4 py-2 border border-border rounded bg-background text-foreground" />
                    <input type="hidden" name="imageUrl" id="form-imageUrl" />
                    <p id="image-status" class="text-sm text-muted-foreground mt-1"></p>
                    <button type="button" onclick="uploadImage()" class="mt-2 px-4 py-2 bg-secondary text-secondary-foreground rounded text-sm hover:opacity-90 transition">
                        Upload Image
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Category</label>
                        <select name="category" id="form-category" class="w-full px-4 py-2 border border-border rounded bg-background text-foreground">
                            <option value="">Select a category</option>
                            <?php foreach ($categoryOptions as $opt): ?>
                                <option value="<?php echo htmlspecialchars($opt); ?>"><?php echo htmlspecialchars($opt); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1">Stock Quantity</label>
                        <input type="number" name="stockQuantity" id="form-stockQuantity" value="0" class="w-full px-4 py-2 border border-border rounded bg-background text-foreground" />
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="inStock" id="form-inStock" checked class="w-4 h-4" />
                    <label class="text-sm text-foreground">In Stock</label>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Marketing Tags</label>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="tags[]" value="bestsellers" class="w-4 h-4" />
                            Best Sellers
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="tags[]" value="newarrivals" class="w-4 h-4" />
                            New Arrivals
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="tags[]" value="bestreviewed" class="w-4 h-4" />
                            Best Reviewed
                        </label>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-primary text-primary-foreground rounded hover:opacity-90 transition">
                        Save
                    </button>
                    <button type="button" onclick="closeProductModal()" class="flex-1 px-4 py-2 bg-muted text-foreground rounded hover:opacity-90 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openProductModal() {
    document.getElementById('product-modal').classList.remove('hidden');
    document.getElementById('product-modal').classList.add('flex');
    document.getElementById('modal-title').textContent = 'Add Product';
    document.getElementById('form-action').value = 'create';
    document.getElementById('product-form').reset();
}

function closeProductModal() {
    document.getElementById('product-modal').classList.add('hidden');
    document.getElementById('product-modal').classList.remove('flex');
}

function editProduct(product) {
    openProductModal();
    document.getElementById('modal-title').textContent = 'Edit Product';
    document.getElementById('form-action').value = 'update';
    document.getElementById('form-id').value = product.id;
    document.getElementById('form-name').value = product.name;
    document.getElementById('form-price').value = product.price.replace('₵', '').replace(/,/g, '');
    document.getElementById('form-description').value = product.description || '';
    document.getElementById('form-imageUrl').value = product.imageUrl || '';
    document.getElementById('form-category').value = product.category || '';
    document.getElementById('form-stockQuantity').value = product.stockQuantity || 0;
    document.getElementById('form-inStock').checked = product.inStock;
    
    // Set tags
    const tags = product.tags || [];
    document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
        checkbox.checked = tags.includes(checkbox.value);
    });
}

function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="${id}">
    `;
    document.body.appendChild(form);
    form.submit();
}

async function uploadImage() {
    const fileInput = document.getElementById('image-upload');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select an image file');
        return;
    }
    
    const formData = new FormData();
    formData.append('image', file);
    
    document.getElementById('image-status').textContent = 'Uploading...';
    
    try {
        const response = await fetch('<?php echo url('admin/upload-image.php'); ?>', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('form-imageUrl').value = result.url;
            document.getElementById('image-status').textContent = 'Uploaded: ' + result.url;
        } else {
            alert('Upload failed: ' + (result.message || 'Unknown error'));
            document.getElementById('image-status').textContent = 'Upload failed';
        }
    } catch (error) {
        alert('Upload failed. Please try again.');
        document.getElementById('image-status').textContent = 'Upload failed';
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>

