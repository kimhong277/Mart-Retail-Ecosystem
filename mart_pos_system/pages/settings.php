<?php
// pages/settings.php
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$conn = mysqli_connect($host, $db_user, $db_pass, 'mart_pos_system');

// Extract global system configuration key-value array pairs
$settings_query = mysqli_query($conn, "SELECT setting_key, setting_value FROM system_settings");
$sys = [];
if ($settings_query) {
    while ($row = mysqli_fetch_assoc($settings_query)) {
        $sys[$row['setting_key']] = $row['setting_value'];
    }
}

// Fallback defaults if the seed rows are missing
$store_name  = $sys['store_name'] ?? 'MINI POS STORE';
$store_phone = $sys['store_phone'] ?? '012345678';
$store_email = $sys['store_email'] ?? 'support@martdomain.com';
$store_addr  = $sys['store_address'] ?? 'Phnom Penh, Cambodia';
$currency    = $sys['currency_symbol'] ?? '$';
?>

<div class="container-fluid px-4 pt-4">
    <div class="mb-4">
        <span class="text-primary fw-bold text-uppercase small d-block mb-1" style="letter-spacing: 0.05em;">System Preferences</span>
        <h2 class="h3 mb-0 text-dark fw-bold">Global System Settings</h2>
        <p class="text-muted small">Update your overall storefront brand profile identity and global invoice properties layout parameters.</p>
    </div>

    <div class="row g-4">
        <!-- Main Column: Store Identity Config Matrix -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm border-0 p-4 bg-white rounded-4">
                <h5 class="fw-bold text-dark mb-4"><i class="bi bi-shop me-2 text-primary"></i>Store Profile Settings</h5>

                <form action="process_settings.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Store / Brand Name</label>
                            <input type="text" name="store_name" class="form-control text-dark" value="<?= htmlspecialchars($store_name) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary">Operational Hot-line</label>
                            <input type="text" name="store_phone" class="form-control text-dark" value="<?= htmlspecialchars($store_phone) ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-secondary">Support Email Address</label>
                            <input type="email" name="store_email" class="form-control text-dark" value="<?= htmlspecialchars($store_email) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary">System Currency Token</label>
                            <select name="currency_symbol" class="form-select font-monospace text-dark">
                                <option value="$" <?= $currency === '$' ? 'selected' : '' ?>>$ (USD Dollar)</option>
                                <option value="៛" <?= $currency === '៛' ? 'selected' : '' ?>>៛ (Khmer Riel)</option>
                                <option value="€" <?= $currency === '€' ? 'selected' : '' ?>>€ (Euro Region)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-secondary">Physical Address (Receipt Header Text)</label>
                            <textarea name="store_address" class="form-control text-dark" rows="3"><?= htmlspecialchars($store_addr) ?></textarea>
                            <div class="form-text text-muted" style="font-size:0.75rem;">This address formatting block injects straight into print invoice preview panels.</div>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" name="update_store_settings" class="btn btn-primary fw-bold px-4 shadow-sm rounded-3">
                                <i class="bi bi-save-fill me-1"></i> Save Enterprise Config
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Explanatory Meta Container Column -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm border-0 p-4 bg-light rounded-4 border-start border-4 border-primary h-100">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-primary me-2"></i>Global Context</h6>
                <p class="small text-secondary mb-3">Modifications submitted through this terminal interface change system configurations programmatically across all connected device endpoints.</p>
                <hr class="opacity-25 my-3">
                <ul class="small text-secondary ps-3 mb-0">
                    <li class="mb-2">Receipt headers mirror this structural text directly.</li>
                    <li class="mb-2">Currency selections alter transactional checkouts dynamically.</li>
                    <li>Adjustments are audited inside the centralized database table state keys registry.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 🧠 SWAL REDIRECT STATUS LISTENER -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const statusMsg = urlParams.get('status');

        if (statusMsg) {
            if (statusMsg === 'updated') {
                Swal.fire({
                    title: 'System Reconfigured!',
                    text: 'Global application registry data variables have been committed securely.',
                    icon: 'success',
                    confirmButtonColor: '#0d6efd',
                    timer: 2000
                });
            } else if (statusMsg === 'error') {
                Swal.fire({
                    title: 'Configuration Error!',
                    text: 'A transactional database failure blocked the execution loop parameters modification write.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
            window.history.replaceState({}, document.title, window.location.pathname + "?page=settings");
        }
    });
</script>