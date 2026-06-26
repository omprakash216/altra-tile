<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['admin_logged_in'])) { header('Location: index.php'); exit(); }
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/layout.php';

$pdo = db();
$msg = '';
$err = '';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// ---- HERO CONTENT ----
if ($requestMethod === 'POST' && isset($_POST['save_content'])) {
    $stmt = $pdo->prepare("UPDATE hero_content SET eyebrow=?, headline=?, headline_highlight=?, subtext=?, btn_primary_text=?, btn_primary_href=?, btn_secondary_text=?, btn_secondary_href=? WHERE id=1");
    $stmt->execute([
        $_POST['eyebrow'], $_POST['headline'], $_POST['headline_highlight'],
        $_POST['subtext'], $_POST['btn_primary_text'], $_POST['btn_primary_href'],
        $_POST['btn_secondary_text'], $_POST['btn_secondary_href']
    ]);
    $msg = 'Hero content updated successfully!';
}

// ---- ADD SLIDE ----
if ($requestMethod === 'POST' && isset($_POST['add_slide'])) {
    $imgPath = trim($_POST['slide_image']);
    $uploadError = null;
    $uploadedImage = upload_asset_file('slide_image_file', $uploadError, null, 'hero_');

    if ($uploadError) {
        $err = $uploadError;
    } elseif ($uploadedImage) {
        $imgPath = $uploadedImage;
    }

    if (!$err && $imgPath) {
        $sort = (int)$pdo->query("SELECT MAX(sort_order) FROM hero_slides")->fetchColumn() + 1;
        $pdo->prepare("INSERT INTO hero_slides (image, sort_order) VALUES (?, ?)")->execute([$imgPath, $sort]);
        $msg = 'Slide added!';
    } elseif (!$err) {
        $err = 'Please upload a slider image or enter an image path.';
    }
}

// ---- DELETE SLIDE ----
if (isset($_GET['del_slide'])) {
    $pdo->prepare("DELETE FROM hero_slides WHERE id=?")->execute([(int)$_GET['del_slide']]);
    $msg = 'Slide deleted.';
}

// ---- TOGGLE SLIDE ----
if (isset($_GET['toggle_slide'])) {
    $pdo->prepare("UPDATE hero_slides SET is_active = 1 - is_active WHERE id=?")->execute([(int)$_GET['toggle_slide']]);
    header('Location: hero.php'); exit();
}

$content = $pdo->query("SELECT * FROM hero_content LIMIT 1")->fetch();
$slides  = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();
$heroPreviewSlide = '';
$activeSlideCount = 0;
foreach ($slides as $slide) {
    if (!empty($slide['is_active'])) {
        $activeSlideCount++;
        if ($heroPreviewSlide === '') {
            $heroPreviewSlide = normalize_asset_path($slide['image'] ?? '');
        }
    }
}
if ($heroPreviewSlide === '' && !empty($slides)) {
    $heroPreviewSlide = normalize_asset_path($slides[0]['image'] ?? '');
}

$openModalId = '';
$openModalTab = 'copy';
if ($err) {
    if ($requestMethod === 'POST' && isset($_POST['add_slide'])) {
        $openModalId = 'hero-upload-modal';
    } else {
        $openModalId = 'hero-manager-modal';
    }
}

admin_head('Hero Slider');
?>
<div class="admin-layout">
<?php admin_sidebar('hero'); ?>
<div class="main-content">
<?php admin_topbar('Hero Slider', 'Manage homepage hero section'); ?>
<div class="page-body">

<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<div class="editor-shell" style="margin-bottom:24px">
  <div class="editor-header hero-header-bar">
    <div>
      <div class="editor-kicker">Homepage Hero</div>
      <div class="editor-title">Hero Slider</div>
      <div class="editor-subtitle">Manage homepage banners from a clean header. The add form stays hidden until you click the button.</div>
    </div>
    <div class="hero-header-actions">
      <button type="button" class="btn btn-primary" data-modal-open="hero-upload-modal">Add Banner</button>
      <button type="button" class="btn btn-secondary" data-modal-open="hero-manager-modal" data-modal-tab="copy">Edit Copy</button>
    </div>
  </div>

  <div class="hero-overview-stats">
    <div class="hero-overview-metric">
      <div class="hero-overview-metric-label">Total Slides</div>
      <div class="hero-overview-metric-value"><?= count($slides) ?></div>
      <div class="hero-overview-metric-note">All homepage hero banners</div>
    </div>
    <div class="hero-overview-metric">
      <div class="hero-overview-metric-label">Active Slides</div>
      <div class="hero-overview-metric-value"><?= $activeSlideCount ?></div>
      <div class="hero-overview-metric-note">Visible on the website</div>
    </div>
    <div class="hero-overview-metric">
      <div class="hero-overview-metric-label">Best Banner Size</div>
      <div class="hero-overview-metric-value">2:3 Portrait</div>
      <div class="hero-overview-metric-note">Use 1200 x 1800 or bigger</div>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="hero-manager-modal" data-modal hidden>
  <div class="modal modal--wide">
    <div class="modal-header">
      <div>
        <div class="modal-kicker">Homepage Hero</div>
        <div class="modal-title">Hero Manager</div>
        <div class="modal-subtitle">Edit hero copy and preview the current slides from one popup.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close popup">&times;</button>
    </div>

    <div class="hero-manager-tabs" role="tablist" aria-label="Hero manager tabs">
      <button type="button" class="hero-manager-tab" data-tab-target="copy" role="tab">Hero Copy</button>
      <button type="button" class="hero-manager-tab" data-tab-target="preview" role="tab">Live Preview</button>
    </div>

    <div class="hero-manager-panels">
      <section class="hero-manager-panel" data-tab-panel="copy">
        <div class="editor-section-title">Hero Text Content</div>
        <div class="editor-section-desc">Update the copy shown on the homepage hero banner.</div>
        <form method="POST" class="form-grid">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Eyebrow Text</label>
              <input name="eyebrow" class="form-control" value="<?= htmlspecialchars($content['eyebrow'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Headline Highlight Word(s)</label>
              <input name="headline_highlight" class="form-control" value="<?= htmlspecialchars($content['headline_highlight'] ?? '') ?>">
              <span class="form-hint">This portion will appear in orange color</span>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Full Headline</label>
            <input name="headline" class="form-control" value="<?= htmlspecialchars($content['headline'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Subtext / Description</label>
            <textarea name="subtext" class="form-control"><?= htmlspecialchars($content['subtext'] ?? '') ?></textarea>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Primary Button Text</label>
              <input name="btn_primary_text" class="form-control" value="<?= htmlspecialchars($content['btn_primary_text'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Primary Button Link</label>
              <input name="btn_primary_href" class="form-control" value="<?= htmlspecialchars($content['btn_primary_href'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Secondary Button Text</label>
              <input name="btn_secondary_text" class="form-control" value="<?= htmlspecialchars($content['btn_secondary_text'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Secondary Button Link</label>
              <input name="btn_secondary_href" class="form-control" value="<?= htmlspecialchars($content['btn_secondary_href'] ?? '') ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
            <button type="submit" name="save_content" class="btn btn-primary">Save Hero Content</button>
          </div>
        </form>
      </section>

      <section class="hero-manager-panel" data-tab-panel="preview" hidden>
        <div class="hero-manager-panel-grid">
          <div>
            <div class="editor-section-title">Current Hero Preview</div>
            <div class="editor-section-desc"><?= count($slides) ?> slides total, <?= $activeSlideCount ?> active.</div>
            <div class="hero-preview-box">
              <?php if ($heroPreviewSlide): ?>
                <img src="../../public<?= htmlspecialchars($heroPreviewSlide) ?>" alt="Hero preview" onerror="this.parentElement.innerHTML='<div class=&quot;editor-preview-empty&quot;>Preview unavailable</div>'">
              <?php else: ?>
                <div class="editor-preview-empty">Upload the first slider image to preview it here.</div>
              <?php endif; ?>
            </div>
          </div>
          <div>
            <div class="editor-section-title">Slide List</div>
            <div class="editor-section-desc">Use the table below to toggle visibility or delete slides.</div>
            <div class="hero-preview-list">
              <?php foreach ($slides as $index => $slide): ?>
                <div class="hero-preview-item">
                  <img class="hero-preview-thumb" src="../../public<?= htmlspecialchars($slide['image']) ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2256%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%231a2235%22/></svg>'">
                  <div>
                    <strong>Slide <?= $index + 1 ?></strong>
                    <div><?= htmlspecialchars($slide['image']) ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
              <?php if (empty($slides)): ?>
                <div class="hero-preview-item">No slides added yet. Use the Add Banner popup to add the first banner.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </section>
</div>
</div>
</div>

<div class="modal-backdrop" id="hero-upload-modal" data-modal hidden>
  <div class="modal modal--upload">
    <div class="modal-header">
      <div>
        <div class="modal-kicker">Homepage Hero</div>
        <div class="modal-title">Add Banner</div>
        <div class="modal-subtitle">Upload a new hero slide in a focused popup without extra clutter.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close popup">&times;</button>
    </div>

    <div class="hero-upload-grid">
      <form method="POST" enctype="multipart/form-data" class="form-grid hero-upload-form">
        <div class="upload-tip-card">
          <div class="editor-section-title">Banner Upload</div>
          <div class="editor-section-desc">Use a portrait image for the cleanest fit in the current hero slider.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Upload Slider Image</label>
          <input type="file" name="slide_image_file" class="form-control" accept="image/*">
          <span class="form-hint">Recommended size: 1200 x 1800 or any 2:3 portrait image.</span>
        </div>

        <div class="form-group">
          <label class="form-label">Image Path (fallback)</label>
          <input name="slide_image" class="form-control" placeholder="/assets/12.jpeg">
          <span class="form-hint">Use this only if the image already exists in assets.</span>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
          <button type="submit" name="add_slide" class="btn btn-primary">Add Slide</button>
        </div>
      </form>

      <aside class="hero-upload-side">
        <div class="editor-section-title">Current Preview</div>
        <div class="editor-section-desc">This is the latest active slide from the homepage.</div>
        <div class="hero-preview-box hero-upload-preview">
          <?php if ($heroPreviewSlide): ?>
            <img src="../../public<?= htmlspecialchars($heroPreviewSlide) ?>" alt="Hero preview" onerror="this.parentElement.innerHTML='<div class=&quot;editor-preview-empty&quot;>Preview unavailable</div>'">
          <?php else: ?>
            <div class="editor-preview-empty">Upload the first slider image to preview it here.</div>
          <?php endif; ?>
        </div>

        <div class="hero-upload-notes">
          <div class="hero-upload-note">
            <strong>Best ratio</strong>
            <span>2:3 portrait</span>
          </div>
          <div class="hero-upload-note">
            <strong>Safe area</strong>
            <span>Keep the product centered</span>
          </div>
          <div class="hero-upload-note">
            <strong>Fallback</strong>
            <span>Use an existing /assets path if needed</span>
          </div>
        </div>
      </aside>
    </div>
  </div>
</div>

<!-- Slides Manager -->
<div class="card">
  <div class="flex items-center gap-3" style="margin-bottom:18px">
    <div class="card-title" style="margin:0">Hero Slides (<?= count($slides) ?>)</div>
  </div>

  <div class="table-wrap" style="margin-bottom:22px">
    <table>
      <thead><tr><th>Preview</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($slides as $slide): ?>
        <tr>
          <td><img class="td-img" src="../../public<?= htmlspecialchars($slide['image']) ?>" alt="" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2250%22 height=%2238%22><rect width=%22100%25%22 height=%22100%25%22 fill=%22%231a2235%22/></svg>'"></td>
          <td><?= $slide['sort_order'] ?></td>
          <td>
            <a href="?toggle_slide=<?= $slide['id'] ?>" class="td-badge <?= $slide['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
              <?= $slide['is_active'] ? 'Active' : 'Hidden' ?>
            </a>
          </td>
          <td>
            <a href="?del_slide=<?= $slide['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this slide?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

</div>
</div>
</div>

<script>
(function () {
  const modals = Array.from(document.querySelectorAll('[data-modal]'));
  if (!modals.length) return;

  const modalMap = new Map(modals.map((modal) => [modal.id, modal]));
  const openers = document.querySelectorAll('[data-modal-open]');
  const initialModalId = <?= json_encode($openModalId) ?>;
  const initialModalTab = <?= json_encode($openModalTab) ?>;

  function setTab(modal, tab) {
    if (!modal) return;
    const tabs = Array.from(modal.querySelectorAll('[data-tab-target]'));
    const panels = Array.from(modal.querySelectorAll('[data-tab-panel]'));
    if (!tabs.length || !panels.length) return;

    const activeTab = tab || tabs[0].dataset.tabTarget;
    tabs.forEach((button) => {
      button.classList.toggle('active', button.dataset.tabTarget === activeTab);
    });
    panels.forEach((panel) => {
      panel.hidden = panel.dataset.tabPanel !== activeTab;
    });
  }

  function refreshBodyState() {
    const anyOpen = modals.some((modal) => !modal.hidden);
    document.body.classList.toggle('modal-open', anyOpen);
  }

  function openModal(id, tab) {
    const modal = modalMap.get(id);
    if (!modal) return;
    modal.hidden = false;
    setTab(modal, tab);
    refreshBodyState();
  }

  function closeModal(modal) {
    modal.hidden = true;
    refreshBodyState();
  }

  openers.forEach((button) => {
    button.addEventListener('click', () => {
      openModal(button.dataset.modalOpen, button.dataset.modalTab || 'copy');
    });
  });

  modals.forEach((modal) => {
    const tabs = Array.from(modal.querySelectorAll('[data-tab-target]'));
    const closeButtons = modal.querySelectorAll('[data-modal-close]');

    tabs.forEach((button) => {
      button.addEventListener('click', () => setTab(modal, button.dataset.tabTarget));
    });

    closeButtons.forEach((button) => {
      button.addEventListener('click', () => closeModal(modal));
    });

    modal.addEventListener('click', (event) => {
      if (event.target === modal) closeModal(modal);
    });

    setTab(modal);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      modals.forEach((modal) => {
        if (!modal.hidden) closeModal(modal);
      });
    }
  });

  if (initialModalId) {
    openModal(initialModalId, initialModalTab);
  }
})();
</script>
</body>
</html>
