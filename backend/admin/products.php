<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = ''; $err = '';
$editItem = null;
$editCategory = null;
$editSubItem = null;
$tab = $_GET['tab'] ?? 'products'; // products | categories | subcategories
if (!in_array($tab, ['products', 'categories', 'subcategories'], true)) {
    $tab = 'products';
}

function pretty_json_text(?string $value): string {
    if (!$value) return '';
    $decoded = json_decode($value, true);
    if (!is_array($decoded)) return $value;
    return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// ========= PRODUCTS TAB =========
if ($tab === 'products') {
    // ADD / EDIT PRODUCT
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
        $id    = (int)($_POST['id'] ?? 0);
        $slug  = trim($_POST['slug']);
        $title = trim($_POST['title']);
        $cat   = trim($_POST['category_filter']);
        $img   = trim($_POST['image']);
        $desc  = trim($_POST['description']);
        $feats = array_values(array_filter(array_map('trim', explode("\n", $_POST['features']))));
        $sort  = (int)$_POST['sort_order'];
        $active= isset($_POST['is_active']) ? 1 : 0;
        $existingProduct = null;

        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
            $stmt->execute([$id]);
            $existingProduct = $stmt->fetch() ?: null;
        }

        $uploadError = null;
        $uploadedImage = upload_asset_file('image_file', $uploadError, null, 'product_');
        if ($uploadError) {
            $err = $uploadError;
        } elseif ($uploadedImage) {
            $img = $uploadedImage;
        }

        if (!$err && $img === '') {
            $img = $existingProduct['image'] ?? '';
        }

        if (!$err && $img === '') {
            $err = 'Please upload an image or enter an image path.';
        }

        if (!$err) {
            if ($id) {
                $pdo->prepare("UPDATE products SET slug=?,title=?,category_filter=?,image=?,description=?,features=?,sort_order=?,is_active=? WHERE id=?")
                    ->execute([$slug,$title,$cat,$img,$desc,json_encode($feats),$sort,$active,$id]);
                $msg = 'Product updated!';
            } else {
                $pdo->prepare("INSERT INTO products (slug,title,category_filter,image,description,features,sort_order,is_active) VALUES (?,?,?,?,?,?,?,?)")
                    ->execute([$slug,$title,$cat,$img,$desc,json_encode($feats),$sort,$active]);
                $msg = 'Product added!';
            }
            header('Location: products.php?tab=products&msg=' . urlencode($msg)); exit();
        }

        $editItem = [
            'id' => $id,
            'slug' => $slug,
            'title' => $title,
            'category_filter' => $cat,
            'image' => $img,
            'description' => $desc,
            'features_text' => implode("\n", $feats),
            'sort_order' => $sort,
            'is_active' => $active,
        ];
    }
    // DELETE PRODUCT
    if (isset($_GET['del'])) {
        $pdo->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_GET['del']]);
        header('Location: products.php?tab=products&msg=Product+deleted'); exit();
    }
    // EDIT FETCH
    if (isset($_GET['edit'])) {
        $editItem = $pdo->prepare("SELECT * FROM products WHERE id=?");
        $editItem->execute([(int)$_GET['edit']]);
        $editItem = $editItem->fetch();
        if ($editItem) $editItem['features_text'] = implode("\n", json_decode($editItem['features'] ?? '[]', true));
    }
}

// ========= CATEGORIES TAB =========
if ($tab === 'categories') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
        $id    = (int)($_POST['id'] ?? 0);
        $slug  = trim($_POST['slug']);
        $name  = trim($_POST['name']);
        $img   = trim($_POST['image']);
        $desc  = trim($_POST['description']);
        $feats = array_values(array_filter(array_map('trim', explode("\n", $_POST['features']))));
        $sort  = (int)$_POST['sort_order'];
        $active = isset($_POST['is_active']) ? 1 : 0;
        $existingCategory = null;

        if ($id) {
            $st = $pdo->prepare("SELECT * FROM product_categories WHERE id=?");
            $st->execute([$id]);
            $existingCategory = $st->fetch() ?: null;
        }

        $uploadError = null;
        $uploadedImage = upload_asset_file('image_file', $uploadError, null, 'cat_');
        if ($uploadError) {
            $err = $uploadError;
        } elseif ($uploadedImage) {
            $img = $uploadedImage;
        }

        if (!$err && $img === '') {
            $img = $existingCategory['image'] ?? '';
        }

        if (!$err && $img === '') {
            $err = 'Please upload a category image or enter an image path.';
        }

        if (!$err) {
            if ($id) {
                $pdo->prepare("UPDATE product_categories SET slug=?,name=?,image=?,description=?,features=?,sort_order=?,is_active=? WHERE id=?")
                    ->execute([$slug,$name,$img,$desc,json_encode($feats),$sort,$active,$id]);
                $msg = 'Category updated!';
            } else {
                $pdo->prepare("INSERT INTO product_categories (slug,name,image,description,features,sort_order,is_active) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$slug,$name,$img,$desc,json_encode($feats),$sort,$active]);
                $msg = 'Category added!';
            }
            header('Location: products.php?tab=categories&msg=' . urlencode($msg)); exit();
        }

        $editCategory = [
            'id' => $id,
            'slug' => $slug,
            'name' => $name,
            'image' => $img,
            'description' => $desc,
            'features_text' => implode("\n", $feats),
            'sort_order' => $sort,
            'is_active' => $active,
        ];
    }
    if (isset($_GET['del_cat'])) {
        $pdo->prepare("DELETE FROM product_categories WHERE id=?")->execute([(int)$_GET['del_cat']]);
        header('Location: products.php?tab=categories&msg=Category+deleted'); exit();
    }
    if (isset($_GET['edit_cat'])) {
        $editCategory = $pdo->prepare("SELECT * FROM product_categories WHERE id=?");
        $editCategory->execute([(int)$_GET['edit_cat']]);
        $editCategory = $editCategory->fetch();
        if ($editCategory) $editCategory['features_text'] = implode("\n", json_decode($editCategory['features'] ?? '[]', true));
    }
}

// ========= SUBCATEGORIES TAB =========
if ($tab === 'subcategories') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_subcategory'])) {
        $id = (int)($_POST['id'] ?? 0);
        $slug = trim($_POST['slug']);
        $categorySlug = trim($_POST['category_slug']);
        $name = trim($_POST['name']);
        $image = trim($_POST['image']);
        $description = trim($_POST['description']);
        $specs = trim($_POST['specs']);
        $features = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $_POST['features'] ?? ''))));
        $sort = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;

        $existingSubItem = null;
        if ($id) {
            $st = $pdo->prepare("SELECT * FROM product_subitems WHERE id=?");
            $st->execute([$id]);
            $existingSubItem = $st->fetch() ?: null;
        }

        $uploadError = null;
        $uploadedImage = upload_asset_file('image_file', $uploadError, null, 'subcat_');
        if ($uploadError) {
            $err = $uploadError;
        } elseif ($uploadedImage) {
            $image = $uploadedImage;
        }

        if (!$err && $image === '') {
            $image = $existingSubItem['image'] ?? '';
        }

        if (!$err && !$categorySlug) {
            $err = 'Please select a parent category.';
        }

        $decodedSpecs = json_decode($specs, true);
        $specsJson = json_encode(is_array($decodedSpecs) ? $decodedSpecs : []);

        if (!$err) {
            if ($id) {
                $pdo->prepare("UPDATE product_subitems SET slug=?, category_slug=?, name=?, image=?, description=?, specs=?, features=?, sort_order=?, is_active=? WHERE id=?")
                    ->execute([$slug, $categorySlug, $name, $image, $description, $specsJson, json_encode($features), $sort, $active, $id]);
                $msg = 'Sub category updated!';
            } else {
                $pdo->prepare("INSERT INTO product_subitems (slug, category_slug, name, image, description, specs, features, sort_order, is_active) VALUES (?,?,?,?,?,?,?,?,?)")
                    ->execute([$slug, $categorySlug, $name, $image, $description, $specsJson, json_encode($features), $sort, $active]);
                $msg = 'Sub category added!';
            }
            header('Location: products.php?tab=subcategories&msg=' . urlencode($msg)); exit();
        }

        $editSubItem = [
            'id' => $id,
            'slug' => $slug,
            'category_slug' => $categorySlug,
            'name' => $name,
            'image' => $image,
            'description' => $description,
            'specs_text' => pretty_json_text($specs),
            'features_text' => implode("\n", $features),
            'sort_order' => $sort,
            'is_active' => $active,
        ];
    }
    if (isset($_GET['del_sub'])) {
        $pdo->prepare("DELETE FROM product_subitems WHERE id=?")->execute([(int)$_GET['del_sub']]);
        header('Location: products.php?tab=subcategories&msg=Sub+category+deleted'); exit();
    }
    if (isset($_GET['edit_sub'])) {
        $editSubItem = $pdo->prepare("SELECT * FROM product_subitems WHERE id=?");
        $editSubItem->execute([(int)$_GET['edit_sub']]);
        $editSubItem = $editSubItem->fetch();
        if ($editSubItem) {
            $editSubItem['features_text'] = implode("\n", decode_json_field($editSubItem['features'] ?? null));
            $editSubItem['specs_text'] = pretty_json_text($editSubItem['specs'] ?? '');
        }
    }
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];

$products   = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC")->fetchAll();
$categories = $pdo->query("SELECT * FROM product_categories ORDER BY sort_order ASC")->fetchAll();
$subItems   = $pdo->query("SELECT s.*, c.name AS category_name FROM product_subitems s LEFT JOIN product_categories c ON c.slug = s.category_slug ORDER BY s.sort_order ASC")->fetchAll();
$catFilters = array_unique(array_column($categories, 'name'));

admin_head('Products');
?>
<div class="admin-layout">
<?php admin_sidebar($tab === 'subcategories' ? 'subcategories' : 'products'); ?>
<div class="main-content">
<?php admin_topbar('Products Manager', 'Manage products and product categories'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="tabs">
  <a href="?tab=products" class="tab-btn <?= $tab==='products'?'active':'' ?>">Products List</a>
  <a href="?tab=categories" class="tab-btn <?= $tab==='categories'?'active':'' ?>">Product Categories</a>
  <a href="?tab=subcategories" class="tab-btn <?= $tab==='subcategories'?'active':'' ?>">Sub Categories</a>
</div>

<?php if ($tab === 'products'): ?>

<!-- PRODUCTS TABLE -->
<div class="page-header">
  <div><h1>Products (<?= count($products) ?>)</h1><p>Flat product cards shown on homepage</p></div>
  <a href="?tab=products&add=1" class="btn btn-primary">+ Add Product</a>
</div>

<?php if (isset($_GET['add']) || $editItem): ?>
<div class="card" style="margin-bottom:22px">
  <div class="card-title"><?= $editItem ? 'Edit Product' : 'Add New Product' ?></div>
  <form method="POST" class="form-grid" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $editItem['id'] ?? 0 ?>">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Title *</label>
        <input name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" placeholder="Concrete Block Making Machine">
      </div>
      <div class="form-group">
        <label class="form-label">Slug (URL ID) *</label>
        <input name="slug" class="form-control" required value="<?= htmlspecialchars($editItem['slug'] ?? '') ?>" placeholder="concrete-block-making-machine">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Category Filter</label>
        <input name="category_filter" class="form-control" value="<?= htmlspecialchars($editItem['category_filter'] ?? '') ?>" placeholder="Block Machine">
      </div>
      <div class="form-group">
        <label class="form-label">Upload Product Image</label>
        <input type="file" name="image_file" class="form-control" accept="image/*">
        <div class="text-sm text-muted" style="margin-top:8px">Choose a file to upload it directly. If you skip this, the image path below will still work.</div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Image Path</label>
        <input name="image" class="form-control" value="<?= htmlspecialchars(normalize_asset_path($editItem['image'] ?? '')) ?>" placeholder="/assets/1.jpeg">
        <?php if (!empty($editItem['image'])): ?>
          <div style="margin-top:10px">
            <img class="td-img" src="../../public<?= htmlspecialchars(normalize_asset_path($editItem['image'])) ?>" alt="Current product image" style="width:140px;height:auto;object-fit:cover;border-radius:12px">
          </div>
        <?php endif; ?>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label class="form-label">Features (one per line)</label>
      <textarea name="features" class="form-control" rows="4" placeholder="Fast mould change&#10;Servo vibration"><?= htmlspecialchars($editItem['features_text'] ?? '') ?></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input name="sort_order" type="number" class="form-control" value="<?= $editItem['sort_order'] ?? 1 ?>">
      </div>
      <div class="form-group" style="justify-content:flex-end;flex-direction:row;align-items:center;gap:10px;padding-top:24px">
        <label class="form-label" style="margin:0">Active</label>
        <label class="toggle"><input type="checkbox" name="is_active" <?= ($editItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
      </div>
    </div>
    <div style="display:flex;gap:10px">
      <button type="submit" name="save_product" class="btn btn-primary">Save Product</button>
      <a href="?tab=products" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Image</th><th>Title</th><th>Category</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><img class="td-img" src="../../public<?= htmlspecialchars($p['image']) ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2238%22><rect fill=%22%231a2235%22 width=%22100%25%22 height=%22100%25%22/></svg>'"></td>
        <td><strong><?= htmlspecialchars($p['title']) ?></strong><div class="text-sm text-muted"><?= htmlspecialchars($p['slug']) ?></div></td>
        <td><?= htmlspecialchars($p['category_filter']) ?></td>
        <td><?= $p['sort_order'] ?></td>
        <td><span class="td-badge <?= $p['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $p['is_active'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="flex gap-2">
          <a href="?tab=products&edit=<?= $p['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?tab=products&del=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($tab === 'categories'): ?>
<!-- CATEGORIES TABLE -->
<div class="page-header">
  <div><h1>Product Categories (<?= count($categories) ?>)</h1><p>Categories shown in the navbar mega menu</p></div>
  <div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end">
    <a href="?tab=subcategories&add=1" class="btn btn-secondary">+ Add Sub Category</a>
    <a href="?tab=categories&add=1" class="btn btn-primary">+ Add Category</a>
  </div>
</div>

<?php if (isset($_GET['add']) || $editCategory): ?>
<?php $categoryPreview = normalize_asset_path($editCategory['image'] ?? ''); ?>
<div class="editor-shell" style="margin-bottom:22px">
  <div class="editor-header">
    <div class="editor-kicker">Catalog Structure</div>
    <div class="editor-title"><?= $editCategory ? 'Edit Category' : 'Add Category' ?></div>
    <div class="editor-subtitle">Parent categories drive the navbar mega menu and control which sub categories appear under each product family.</div>
    <div class="editor-chip-row">
      <span class="editor-chip">Navbar Menu</span>
      <span class="editor-chip">Homepage Cards</span>
      <span class="editor-chip">Parent Group</span>
    </div>
  </div>
  <div class="editor-body">
    <div class="editor-main">
      <form method="POST" class="form-grid" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editCategory['id'] ?? 0 ?>">
        <div class="editor-section">
          <div class="editor-section-title">Core Details</div>
          <div class="editor-section-desc">Set the visible name and slug used in the URL structure.</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Name *</label>
              <input name="name" class="form-control" required value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>" placeholder="Concrete Block Making Machine">
            </div>
            <div class="form-group">
              <label class="form-label">Slug *</label>
              <input name="slug" class="form-control" required value="<?= htmlspecialchars($editCategory['slug'] ?? '') ?>" placeholder="concrete-block-making-machine">
            </div>
          </div>
        </div>

        <div class="editor-section">
          <div class="editor-section-title">Media & Visibility</div>
          <div class="editor-section-desc">Upload the category artwork or use a manual asset path. The active toggle controls navbar visibility.</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Upload Category Image</label>
              <input type="file" name="image_file" class="form-control" accept="image/*">
              <span class="form-hint">Best for navbar thumbnails and category cards.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Image Path</label>
              <input name="image" class="form-control" value="<?= htmlspecialchars($categoryPreview) ?>" placeholder="/assets/1.jpeg">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Sort Order</label>
              <input name="sort_order" type="number" class="form-control" value="<?= $editCategory['sort_order'] ?? 1 ?>">
            </div>
            <div class="form-group" style="justify-content:flex-end;flex-direction:row;align-items:center;gap:10px;padding-top:24px">
              <label class="toggle"><input type="checkbox" name="is_active" <?= ($editCategory['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
              <label class="form-label" style="margin:0">Active</label>
            </div>
          </div>
        </div>

        <div class="editor-section">
          <div class="editor-section-title">Description & Features</div>
          <div class="editor-section-desc">Use this section to describe the category and the key benefits it should show in the UI.</div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($editCategory['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Features (one per line)</label>
            <textarea name="features" class="form-control" rows="4"><?= htmlspecialchars($editCategory['features_text'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="editor-actions">
          <button type="submit" name="save_category" class="btn btn-primary">Save Category</button>
          <a href="?tab=categories" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>

    <aside class="editor-side">
      <div class="editor-side-card">
        <h4>Category Preview</h4>
        <p>This visual block mirrors the thumbnail shown in the navbar and category pages.</p>
        <div class="editor-preview" style="margin-top:14px">
          <?php if ($categoryPreview): ?>
            <img src="../../public<?= htmlspecialchars($categoryPreview) ?>" alt="" onerror="this.parentElement.innerHTML='<div class=&quot;editor-preview-empty&quot;>Preview unavailable</div>'">
          <?php else: ?>
            <div class="editor-preview-empty">Upload a category image to see the preview here.</div>
          <?php endif; ?>
        </div>
      </div>
      <div class="editor-side-card">
        <h4>Why this matters</h4>
        <div class="editor-side-list">
          <div class="editor-side-item"><div><strong>Navbar</strong>The category image and title appear in the mega menu.</div></div>
          <div class="editor-side-item"><div><strong>Filtering</strong> The slug is used for category routes and product grouping.</div></div>
          <div class="editor-side-item"><div><strong>Visibility</strong> Inactive categories stay hidden from the front-end.</div></div>
        </div>
      </div>
    </aside>
  </div>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Sort</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($categories as $cat): ?>
      <tr>
        <td><img class="td-img" src="../../public<?= htmlspecialchars($cat['image']) ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2238%22><rect fill=%22%231a2235%22 width=%22100%25%22 height=%22100%25%22/></svg>'"></td>
        <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
        <td><code style="font-size:0.8rem"><?= htmlspecialchars($cat['slug']) ?></code></td>
        <td><?= $cat['sort_order'] ?></td>
        <td><span class="td-badge <?= $cat['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $cat['is_active'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="flex gap-2">
          <a href="?tab=categories&edit_cat=<?= $cat['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?tab=categories&del_cat=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete category and all its sub-products?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($tab === 'subcategories'): ?>
<!-- SUB CATEGORIES TABLE -->
<div class="page-header">
  <div><h1>Sub Categories (<?= count($subItems) ?>)</h1><p>These entries power the navbar mega menu and product pages</p></div>
  <a href="?tab=subcategories&add=1" class="btn btn-primary">+ Add Sub Category</a>
</div>

<?php if (isset($_GET['add']) || $editSubItem): ?>
<?php $subPreview = normalize_asset_path($editSubItem['image'] ?? ''); ?>
<div class="editor-shell" style="margin-bottom:22px">
  <div class="editor-header">
    <div class="editor-kicker">Product Hierarchy</div>
    <div class="editor-title"><?= $editSubItem ? 'Edit Sub Category' : 'Add Sub Category' ?></div>
    <div class="editor-subtitle">Sub categories are the actual models shown in the mega menu and product detail pages.</div>
    <div class="editor-chip-row">
      <span class="editor-chip">Mega Menu</span>
      <span class="editor-chip">Product Page</span>
      <span class="editor-chip">Technical Specs</span>
    </div>
  </div>
  <div class="editor-body">
    <div class="editor-main">
      <form method="POST" class="form-grid" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editSubItem['id'] ?? 0 ?>">
        <div class="editor-section">
          <div class="editor-section-title">Core Details</div>
          <div class="editor-section-desc">Define the model name, slug and parent category relationship.</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Name *</label>
              <input name="name" class="form-control" required value="<?= htmlspecialchars($editSubItem['name'] ?? '') ?>" placeholder="QS1000 Supersonic Block Machine">
            </div>
            <div class="form-group">
              <label class="form-label">Slug *</label>
              <input name="slug" class="form-control" required value="<?= htmlspecialchars($editSubItem['slug'] ?? '') ?>" placeholder="qs1000">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Parent Category *</label>
            <select name="category_slug" class="form-control" required>
              <option value="">Select category</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['slug']) ?>" <?= ($editSubItem['category_slug'] ?? '') === $cat['slug'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="editor-section">
          <div class="editor-section-title">Media & Visibility</div>
          <div class="editor-section-desc">Add a product image, or keep the manual path if you already have a file in assets.</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Upload Sub Category Image</label>
              <input type="file" name="image_file" class="form-control" accept="image/*">
              <span class="form-hint">This image is used in the navbar and product cards.</span>
            </div>
            <div class="form-group">
              <label class="form-label">Image Path</label>
              <input name="image" class="form-control" value="<?= htmlspecialchars($subPreview) ?>" placeholder="/assets/1.jpeg">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Sort Order</label>
              <input name="sort_order" type="number" class="form-control" value="<?= htmlspecialchars($editSubItem['sort_order'] ?? 1) ?>">
            </div>
            <div class="form-group" style="justify-content:flex-end;flex-direction:row;align-items:center;gap:10px;padding-top:24px">
              <label class="toggle"><input type="checkbox" name="is_active" <?= ($editSubItem['is_active'] ?? 1) ? 'checked' : '' ?>><span class="toggle-slider"></span></label>
              <label class="form-label" style="margin:0">Active</label>
            </div>
          </div>
        </div>

        <div class="editor-section">
          <div class="editor-section-title">Description & Technical Data</div>
          <div class="editor-section-desc">Keep the specs JSON valid so the product page can render the technical table cleanly.</div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($editSubItem['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Specs JSON</label>
            <textarea name="specs" class="form-control" rows="5" placeholder='{"Capacity":"1500 Blocks/hr"}'><?= htmlspecialchars($editSubItem['specs_text'] ?? '') ?></textarea>
            <span class="form-hint">Paste valid JSON for the technical specs table.</span>
          </div>
          <div class="form-group">
            <label class="form-label">Features (one per line)</label>
            <textarea name="features" class="form-control" rows="4"><?= htmlspecialchars($editSubItem['features_text'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="editor-actions">
          <button type="submit" name="save_subcategory" class="btn btn-primary">Save Sub Category</button>
          <a href="?tab=subcategories" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>

    <aside class="editor-side">
      <div class="editor-side-card">
        <h4>Model Preview</h4>
        <p>This image and name are what visitors will see in the menu and product pages.</p>
        <div class="editor-preview" style="margin-top:14px">
          <?php if ($subPreview): ?>
            <img src="../../public<?= htmlspecialchars($subPreview) ?>" alt="" onerror="this.parentElement.innerHTML='<div class=&quot;editor-preview-empty&quot;>Preview unavailable</div>'">
          <?php else: ?>
            <div class="editor-preview-empty">Upload a sub category image to preview it here.</div>
          <?php endif; ?>
        </div>
      </div>
      <div class="editor-side-card">
        <h4>What this controls</h4>
        <div class="editor-side-list">
          <div class="editor-side-item"><div><strong>Mega Menu</strong> Appears under the selected parent category.</div></div>
          <div class="editor-side-item"><div><strong>Product Route</strong> Powers the individual product page URL and breadcrumb.</div></div>
          <div class="editor-side-item"><div><strong>Visibility</strong> Hidden items stay out of the public navigation.</div></div>
        </div>
      </div>
    </aside>
  </div>
</div>
<?php endif; ?>

<div class="table-wrap">
  <table>
    <thead><tr><th>Image</th><th>Sub Category</th><th>Parent Category</th><th>Slug</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($subItems as $item): ?>
      <tr>
        <td><img class="td-img" src="../../public<?= htmlspecialchars($item['image']) ?>" alt="" onerror="this.style.display='none'"></td>
        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
        <td><?= htmlspecialchars($item['category_name'] ?? $item['category_slug']) ?></td>
        <td><code style="font-size:0.8rem"><?= htmlspecialchars($item['slug']) ?></code></td>
        <td><?= (int)$item['sort_order'] ?></td>
        <td><span class="td-badge <?= $item['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= $item['is_active'] ? 'Active' : 'Hidden' ?></span></td>
        <td class="flex gap-2">
          <a href="?tab=subcategories&edit_sub=<?= $item['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
          <a href="?tab=subcategories&del_sub=<?= $item['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete sub category?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

</div>
</div>
</div>
</body>
</html>
