<?php
/**
 * The "Add item" modal (HTML fragment). script/general.js loads this into every page
 * and handles opening, the Sell/Swap switch, validation and submitting to submit_item.php.
 * Keep the field names and limits in sync with submit_item.php.
 */
session_start();
header('Content-Type: text/html; charset=utf-8');

// Pre-fill the owner name for logged-in users (login.php stores the username in the session).
$ownerName = htmlspecialchars((string) ($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<div class="modal fade add-item-modal" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="addItemModalTitle">Add an item</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="add-item-form" action="../project-folder/submit_item.php" method="POST" enctype="multipart/form-data" novalidate>
          <div class="alert alert-danger py-2 small" id="add-item-error" role="alert" hidden></div>

          <div class="mb-3">
            <label class="form-label" for="item-name">Item Name</label>
            <input type="text" class="form-control" id="item-name" name="itemName" maxlength="80" placeholder="e.g. Rice Cooker (1L)" required>
            <div class="invalid-feedback" data-error-for="itemName"></div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label" for="item-purpose">Purpose</label>
              <select class="form-select" id="item-purpose" name="purpose" required>
                <option value="sell">Sell</option>
                <option value="swap">Swap</option>
              </select>
              <div class="invalid-feedback" data-error-for="purpose"></div>
            </div>
            <div class="col-6">
              <label class="form-label" for="item-type">Type</label>
              <select class="form-select" id="item-type" name="itemType" required>
                <option value="Kitchen">Kitchen</option>
                <option value="Clothes">Clothes</option>
                <option value="Others">Others</option>
              </select>
              <div class="invalid-feedback" data-error-for="itemType"></div>
            </div>
          </div>

          <div class="mb-3" id="item-price-group">
            <label class="form-label" for="item-price">Item Value (RM)</label>
            <input type="number" class="form-control" id="item-price" name="price" min="0.01" max="99999.99" step="0.01" inputmode="decimal" placeholder="e.g. 25.00" required>
            <div class="invalid-feedback" data-error-for="price"></div>
          </div>

          <div class="mb-3" id="item-wants-group" hidden>
            <label class="form-label" for="item-wants">What do you want in exchange?</label>
            <input type="text" class="form-control" id="item-wants" name="wantsInExchange" maxlength="120" placeholder="e.g. Desk lamp or table fan" required disabled>
            <div class="invalid-feedback" data-error-for="wantsInExchange"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="item-condition">Condition</label>
            <select class="form-select" id="item-condition" name="itemCondition" required>
              <option value="" selected disabled>Choose condition</option>
              <option value="Like New">Like New</option>
              <option value="Good">Good</option>
              <option value="Fair">Fair</option>
            </select>
            <div class="invalid-feedback" data-error-for="itemCondition"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="item-description">Short Description</label>
            <textarea class="form-control" id="item-description" name="description" rows="3" maxlength="200" placeholder="Size, age, any defects, pick-up location…" required></textarea>
            <div class="form-text text-end" id="item-description-count">0 / 200</div>
            <div class="invalid-feedback" data-error-for="description"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="item-owner">Your Name</label>
            <input type="text" class="form-control" id="item-owner" name="ownerName" maxlength="60" value="<?php echo $ownerName; ?>" placeholder="Shown on your listing" autocomplete="name" required>
            <div class="invalid-feedback" data-error-for="ownerName"></div>
          </div>

          <div class="mb-3">
            <label class="form-label" for="item-number">WhatsApp Number</label>
            <input type="tel" class="form-control" id="item-number" name="phoneNumber" maxlength="16" inputmode="tel" placeholder="e.g. 012-3456789" autocomplete="tel" required>
            <div class="invalid-feedback" data-error-for="phoneNumber"></div>
          </div>

          <div class="mb-1">
            <label class="form-label" for="item-image">Item Image <span class="text-muted fw-normal">(optional)</span></label>
            <input type="file" class="form-control" id="item-image" name="itemImage" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
            <div class="form-text">JPG, PNG or WEBP, max 5MB. A placeholder is shown if you skip this.</div>
            <div class="invalid-feedback" data-error-for="itemImage"></div>
            <img id="item-image-preview" class="add-item-preview" alt="Selected image preview" hidden>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="submit" form="add-item-form" class="btn btn-success w-100" id="add-item-submit">Submit</button>
      </div>
    </div>
  </div>
</div>
