<?php
session_start();

include 'connection.php';
include 'utils.php';
include 'includes/auth_validate.php';

$search = isset($_GET['search']) ? $con->real_escape_string(trim($_GET['search'])) : '';

if ($search) {
    $sql = "SELECT v.*, p.product_name, p.product_category, p.product_price AS prod_price
            FROM vendor v
            LEFT JOIN products p ON v.product_id = p.product_id
            WHERE v.vendor_name LIKE '%$search%' OR v.vendor_phone LIKE '%$search%'
            ORDER BY v.id DESC";
} else {
    $sql = "SELECT v.*, p.product_name, p.product_category, p.product_price AS prod_price
            FROM vendor v
            LEFT JOIN products p ON v.product_id = p.product_id
            ORDER BY v.id DESC";
}

$vendors = $con->query($sql);
$total_vendors = $vendors ? $vendors->num_rows : 0;

include('includes/header.php');
?>

<style>
.vendor-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
    margin-top: 4px;
}

.vendor-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}
.vendor-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: hsla(var(--primary-raw), 0.3);
}
.vendor-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    opacity: 0;
    transition: opacity 0.3s ease;
}
.vendor-card:hover::before { opacity: 1; }

.vendor-avatar {
    width: 46px; height: 46px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 18px; font-weight: 800;
    flex-shrink: 0;
}

.vc-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.vc-name { font-weight: 800; font-size: 15px; color: var(--text); }
.vc-phone { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 3px; }

.vc-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
}
.vc-meta-item { }
.vc-meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); margin-bottom: 3px; }
.vc-meta-value { font-size: 13.5px; font-weight: 700; color: var(--text); }

.category-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
    background: hsla(248, 85%, 65%, 0.1);
    color: hsl(248, 85%, 58%);
    border: 1px solid hsla(248, 85%, 65%, 0.2);
}

.vc-actions {
    display: flex; gap: 8px;
}
.vc-btn {
    flex: 1;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 0;
    border-radius: 10px;
    font-size: 12.5px; font-weight: 700;
    text-decoration: none;
    border: 1px solid var(--border);
    background: var(--surface-solid);
    color: var(--text-muted);
    transition: all 0.2s ease;
    cursor: pointer;
}
.vc-btn:hover { border-color: var(--primary); color: var(--primary); text-decoration: none; }
.vc-btn.buy { background: hsl(142,71%,45%); color: white; border-color: transparent; }
.vc-btn.buy:hover { filter: brightness(1.1); color: white; }
.vc-btn.del:hover { border-color: hsl(0,84%,60%); color: hsl(0,84%,60%); }

/* View toggle */
.view-toggle { display: flex; background: var(--bg2); border-radius: 10px; padding: 3px; gap: 2px; }
.view-btn { width: 34px; height: 30px; border: none; background: transparent; cursor: pointer; border-radius: 8px; color: var(--text-muted); font-size: 14px; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; }
.view-btn.active { background: var(--surface-solid); color: var(--text); box-shadow: 0 1px 4px rgba(0,0,0,0.08); }

.search-bar {
    display: flex;
    gap: 12px;
    align-items: center;
    flex: 1;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 10px 16px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.search-inner { position: relative; flex: 1; min-width: 200px; }
.search-inner i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; pointer-events: none; }
.search-inner input { padding-left: 40px !important; }
</style>

<div id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>

    <h1 class="page-header animate-fade-in">
        Vendors
        <a href="add_vendor.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus" style="margin-right: 6px;"></i> Add Vendor
        </a>
    </h1>

    <!-- Search + controls -->
    <div class="search-bar animate-fade-in">
        <form method="GET" action="vendors.php" style="display:flex; gap: 10px; align-items: center; flex: 1; flex-wrap: wrap;">
            <div class="search-inner">
                <i class="fa fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search vendors by name or phone..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Search</button>
            <?php if ($search): ?><a href="vendors.php" class="btn btn-sm" style="background: var(--bg2); border: 1px solid var(--border) !important; color: var(--text-muted);">Clear</a><?php endif; ?>
        </form>

        <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                <?php echo $total_vendors; ?> vendor<?php echo $total_vendors != 1 ? 's' : ''; ?>
            </span>
            <div class="view-toggle">
                <button class="view-btn active" id="grid-btn" onclick="setView('grid')" title="Grid view"><i class="fa fa-grip"></i></button>
                <button class="view-btn" id="list-btn" onclick="setView('list')" title="List view"><i class="fa fa-list"></i></button>
            </div>
        </div>
    </div>

    <!-- Grid view -->
    <div class="vendor-grid animate-fade-in" id="grid-view">
    <?php if ($vendors && $vendors->num_rows > 0):
        foreach ($vendors as $vendor):
            $initial = strtoupper(substr($vendor['vendor_name'], 0, 1));
    ?>
        <div class="vendor-card">
            <div class="vc-header">
                <div class="vendor-avatar"><?php echo $initial; ?></div>
                <div>
                    <div class="vc-name"><?php echo htmlspecialchars($vendor['vendor_name']); ?></div>
                    <div class="vc-phone">
                        <i class="fa fa-phone" style="font-size:10px; color: var(--primary);"></i>
                        <?php echo htmlspecialchars($vendor['vendor_phone']); ?>
                    </div>
                </div>
            </div>

            <div class="vc-meta">
                <div class="vc-meta-item">
                    <div class="vc-meta-label">Product</div>
                    <div class="vc-meta-value"><?php echo htmlspecialchars($vendor['product_name'] ?? '—'); ?></div>
                </div>
                <div class="vc-meta-item">
                    <div class="vc-meta-label">Category</div>
                    <span class="category-chip">
                        <i class="fa fa-tag" style="font-size:9px;"></i>
                        <?php echo htmlspecialchars($vendor['product_category'] ?? '—'); ?>
                    </span>
                </div>
                <div class="vc-meta-item">
                    <div class="vc-meta-label">Stock Qty</div>
                    <div class="vc-meta-value"><?php echo $vendor['vendor_quantity'] ?? '0'; ?></div>
                </div>
                <div class="vc-meta-item">
                    <div class="vc-meta-label">Price</div>
                    <div class="vc-meta-value" style="color: hsl(142,71%,40%);">₹<?php echo number_format($vendor['vendor_price'] ?? 0, 2); ?></div>
                </div>
            </div>

            <div class="vc-actions">
                <a href="edit_vendor.php?id=<?php echo $vendor['id']; ?>" class="vc-btn" title="Edit">
                    <i class="fa fa-pen-to-square"></i> Edit
                </a>
                <a href="buy.php?vendors=true&id=<?php echo $vendor['id']; ?>" class="vc-btn buy">
                    <i class="fa fa-cart-plus"></i> Buy
                </a>
                <a href="utils.php?delete_vendor=true&id=<?php echo $vendor['id']; ?>" 
                   class="vc-btn del ajax-delete" 
                   data-confirm="Delete vendor <?php echo htmlspecialchars($vendor['vendor_name']); ?>?">
                    <i class="fa fa-trash-can"></i>
                </a>
            </div>
        </div>
    <?php endforeach;
    else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 64px 24px; color: var(--text-muted);">
            <i class="fa fa-truck-field" style="font-size: 3rem; opacity: 0.2; display: block; margin-bottom: 16px;"></i>
            <p style="font-weight: 600; font-size: 15px;">No vendors found<?php echo $search ? " for \"$search\"" : ''; ?></p>
            <a href="add_vendor.php" class="btn btn-primary btn-sm" style="margin-top: 16px;"><i class="fa fa-plus"></i> Add First Vendor</a>
        </div>
    <?php endif; ?>
    </div>

    <!-- List view (hidden by default) -->
    <div class="table-responsive animate-fade-in" id="list-view" style="display: none;">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Contact</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($vendors): mysqli_data_seek($vendors, 0); foreach ($vendors as $vendor): ?>
                <tr>
                    <td><span style="font-weight: 700;"><?php echo htmlspecialchars($vendor['vendor_name']); ?></span></td>
                    <td style="font-size: 13px; color: var(--text-muted);"><?php echo $vendor['vendor_phone']; ?></td>
                    <td style="font-weight: 600; font-size: 13px;"><?php echo htmlspecialchars($vendor['product_name'] ?? '—'); ?></td>
                    <td>
                        <span class="category-chip"><?php echo htmlspecialchars($vendor['product_category'] ?? '—'); ?></span>
                    </td>
                    <td style="font-weight: 700;"><?php echo $vendor['vendor_quantity']; ?></td>
                    <td style="font-weight: 700; color: hsl(142,71%,40%);">₹<?php echo number_format($vendor['vendor_price'], 2); ?></td>
                    <td class="text-right">
                        <div style="display: flex; gap: 6px; justify-content: flex-end;">
                            <a href="edit_vendor.php?id=<?php echo $vendor['id']; ?>" class="btn btn-sm" title="Edit"
                               style="background: var(--bg2); border: 1px solid var(--border) !important; color: var(--text-muted);"><i class="fa fa-pen-to-square"></i></a>
                            <a href="buy.php?vendors=true&id=<?php echo $vendor['id']; ?>" class="btn btn-success btn-sm"><i class="fa fa-cart-plus"></i></a>
                            <a href="utils.php?delete_vendor=true&id=<?php echo $vendor['id']; ?>" 
                               class="btn btn-danger btn-sm ajax-delete"
                               data-confirm="Delete this vendor?"><i class="fa fa-trash-can"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function setView(mode) {
    const grid = document.getElementById('grid-view');
    const list = document.getElementById('list-view');
    const gb   = document.getElementById('grid-btn');
    const lb   = document.getElementById('list-btn');
    if (mode === 'grid') {
        grid.style.display = 'grid';
        list.style.display = 'none';
        gb.classList.add('active');
        lb.classList.remove('active');
    } else {
        grid.style.display = 'none';
        list.style.display = 'block';
        lb.classList.add('active');
        gb.classList.remove('active');
    }
    localStorage.setItem('vendors_view', mode);
}
// Restore last view
const savedView = localStorage.getItem('vendors_view');
if (savedView) setView(savedView);
</script>

<?php include 'includes/footer.php'; ?>