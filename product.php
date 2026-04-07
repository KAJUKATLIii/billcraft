<?php
session_start();

include 'connection.php';
include 'utils.php';
include 'includes/auth_validate.php';

$search   = isset($_GET['search'])   ? $con->real_escape_string(trim($_GET['search']))   : '';
$cat      = isset($_GET['category']) ? $con->real_escape_string(trim($_GET['category'])) : '';
$stock_f  = isset($_GET['stock'])    ? $_GET['stock'] : '';

// Get all categories for filter
$cats_res = $con->query("SELECT DISTINCT product_category FROM products ORDER BY product_category");

$where = "1=1";
if ($search) $where .= " AND product_name LIKE '%$search%'";
if ($cat)    $where .= " AND product_category = '$cat'";
if ($stock_f === 'low')  $where .= " AND product_stock < 10 AND product_stock > 0";
if ($stock_f === 'out')  $where .= " AND product_stock <= 0";

$sql = "SELECT * FROM products WHERE $where ORDER BY product_name ASC";
$products = $con->query($sql);
$total_count = $products ? $products->num_rows : 0;

// Stock summary
$stock_stats = $con->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN product_stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
    SUM(CASE WHEN product_stock > 0 AND product_stock < 10 THEN 1 ELSE 0 END) as low_stock,
    SUM(product_stock * product_price) as inventory_value
FROM products")->fetch_assoc();

include('includes/header.php');
?>

<style>
.prod-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
@media(max-width:768px){ .prod-stats { grid-template-columns:repeat(2,1fr); } }

.pstat {
    background: var(--surface); border: 1px solid var(--border); border-radius:12px;
    padding:16px; position:relative; overflow:hidden; transition: transform 0.2s ease;
}
.pstat:hover { transform:translateY(-2px); }
.pstat-label { font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-muted); margin-bottom:4px; }
.pstat-val   { font-size:1.7rem; font-weight:800; letter-spacing:-0.04em; }
.pstat-icon  { position:absolute; right:14px; top:50%; transform:translateY(-50%); font-size:2.2rem; opacity:0.08; }

.filter-bar {
    background:var(--surface); border:1px solid var(--border); border-radius:14px;
    padding:12px 16px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:20px;
}
.search-inner { position:relative; flex:1; min-width:200px; }
.search-inner i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px; pointer-events:none; }
.search-inner input { padding-left:40px !important; }

.cat-chip {
    display:inline-flex; align-items:center; gap:5px; padding:6px 14px;
    border-radius:20px; font-size:12px; font-weight:700; cursor:pointer;
    border:1.5px solid var(--border); background:var(--surface-solid); color:var(--text-muted);
    text-decoration:none; transition:all 0.2s ease; white-space:nowrap;
}
.cat-chip.active { background:var(--primary); color:white; border-color:transparent; }
.cat-chip:hover  { border-color:var(--primary); color:var(--primary); }
.cat-chip.active:hover { filter:brightness(1.08); color:white; }

/* Product cards grid */
.product-grid {
    display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr));
    gap:16px; margin-top:4px;
}
.prod-card {
    background:var(--surface); border:1px solid var(--border); border-radius:16px;
    padding:20px; transition:all 0.25s ease; position:relative; overflow:hidden;
    display:flex; flex-direction:column; gap:12px;
}
.prod-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); border-color:hsla(var(--primary-raw),0.3); }
.prod-card::after {
    content:''; position:absolute; top:0; left:0; right:0; height:3px;
    background:linear-gradient(90deg, var(--primary), var(--accent)); opacity:0; transition:opacity 0.3s ease;
}
.prod-card:hover::after { opacity:1; }

.prod-icon {
    width:44px; height:44px; border-radius:12px; flex-shrink:0;
    background:hsla(var(--primary-raw),0.1);
    display:flex; align-items:center; justify-content:center;
    color:var(--primary); font-size:20px;
}
.prod-header { display:flex; align-items:flex-start; gap:14px; }
.prod-name   { font-weight:800; font-size:15px; color:var(--text); margin-bottom:3px; }
.prod-cat    { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:20px; font-size:10.5px; font-weight:700; background:hsla(var(--primary-raw),0.1); color:var(--primary); border:1px solid hsla(var(--primary-raw),0.2); }

.prod-meta { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.pm-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--text-muted); margin-bottom:2px; }
.pm-val   { font-size:15px; font-weight:800; }

.stock-bar-wrap { }
.stock-bar-label { display:flex; justify-content:space-between; font-size:11px; font-weight:700; margin-bottom:5px; color:var(--text-muted); }
.stock-bar { height:6px; background:var(--bg2); border-radius:3px; overflow:hidden; }
.stock-bar-fill { height:100%; border-radius:3px; transition:width 0.4s ease; }

.prod-actions { display:flex; gap:8px; }
.p-btn {
    flex:1; display:flex; align-items:center; justify-content:center; gap:5px;
    padding:8px 0; border-radius:10px; font-size:12px; font-weight:700;
    text-decoration:none; border:1px solid var(--border); background:var(--surface-solid);
    color:var(--text-muted); transition:all 0.2s ease;
}
.p-btn:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }
.p-btn.del:hover { border-color:hsl(0,84%,60%); color:hsl(0,84%,60%); }

.out-badge {
    position:absolute; top:14px; right:14px;
    background:hsla(0,84%,60%,0.12); color:hsl(0,70%,50%);
    border:1px solid hsla(0,84%,60%,0.25); border-radius:20px;
    padding:3px 10px; font-size:10.5px; font-weight:700;
}
.low-badge {
    position:absolute; top:14px; right:14px;
    background:hsla(38,92%,50%,0.12); color:hsl(38,75%,40%);
    border:1px solid hsla(38,92%,50%,0.25); border-radius:20px;
    padding:3px 10px; font-size:10.5px; font-weight:700;
}
.dark .out-badge { color:hsl(0,84%,68%); }
.dark .low-badge { color:hsl(38,92%,62%); }
</style>

<div id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>

    <h1 class="page-header animate-fade-in">
        Products
        <a href="add_product.php" class="btn btn-primary btn-sm">
            <i class="fa fa-plus" style="margin-right:6px;"></i> Add Product
        </a>
    </h1>

    <!-- Stats strip -->
    <div class="prod-stats animate-fade-in">
        <div class="pstat" style="border-top:3px solid var(--primary);">
            <div class="pstat-label">Total Products</div>
            <div class="pstat-val"><?php echo $stock_stats['total']; ?></div>
            <i class="fa fa-boxes-stacked pstat-icon" style="color:var(--primary);"></i>
        </div>
        <div class="pstat" style="border-top:3px solid hsl(0,84%,60%);">
            <div class="pstat-label">Out of Stock</div>
            <div class="pstat-val" style="color:hsl(0,70%,50%);"><?php echo $stock_stats['out_of_stock']; ?></div>
            <i class="fa fa-ban pstat-icon" style="color:hsl(0,84%,60%);"></i>
        </div>
        <div class="pstat" style="border-top:3px solid hsl(38,92%,50%);">
            <div class="pstat-label">Low Stock (&lt;10)</div>
            <div class="pstat-val" style="color:hsl(38,75%,40%);"><?php echo $stock_stats['low_stock']; ?></div>
            <i class="fa fa-triangle-exclamation pstat-icon" style="color:hsl(38,92%,50%);"></i>
        </div>
        <div class="pstat" style="border-top:3px solid hsl(142,71%,45%);">
            <div class="pstat-label">Inventory Value</div>
            <div class="pstat-val" style="font-size:1.2rem; color:hsl(142,60%,35%);">₹<?php echo number_format($stock_stats['inventory_value'] ?? 0, 0); ?></div>
            <i class="fa fa-indian-rupee-sign pstat-icon" style="color:hsl(142,71%,45%);"></i>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar animate-fade-in">
        <form method="GET" action="product.php" style="display:flex; gap:8px; align-items:center; flex:1; flex-wrap:wrap;">
            <div class="search-inner">
                <i class="fa fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                <?php if($cat): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($cat); ?>"><?php endif; ?>
                <?php if($stock_f): ?><input type="hidden" name="stock" value="<?php echo htmlspecialchars($stock_f); ?>"><?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i></button>
        </form>

        <!-- Category chips -->
        <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center; flex-shrink:0;">
            <a href="product.php<?php echo $search ? '?search='.$search : ''; ?>" class="cat-chip <?php echo !$cat && !$stock_f ? 'active' : ''; ?>">All</a>
            <?php if($cats_res): mysqli_data_seek($cats_res,0); foreach($cats_res as $cc): ?>
            <a href="product.php?category=<?php echo urlencode($cc['product_category']); ?><?php echo $search ? '&search='.$search : ''; ?>"
               class="cat-chip <?php echo $cat === $cc['product_category'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cc['product_category']); ?>
            </a>
            <?php endforeach; endif; ?>
            <a href="product.php?stock=low" class="cat-chip <?php echo $stock_f==='low'?'active':''; ?>" style="<?php echo $stock_f==='low'?'':'color:hsl(38,75%,40%); border-color:hsla(38,92%,50%,0.4);'; ?>">⚠ Low Stock</a>
            <a href="product.php?stock=out" class="cat-chip <?php echo $stock_f==='out'?'active':''; ?>" style="<?php echo $stock_f==='out'?'':'color:hsl(0,70%,50%); border-color:hsla(0,84%,60%,0.4);'; ?>">✕ Out of Stock</a>
        </div>

        <span style="font-size:12px; color:var(--text-muted); font-weight:600; flex-shrink:0;"><?php echo $total_count; ?> products</span>
    </div>

    <!-- Product grid -->
    <div class="product-grid animate-fade-in">
    <?php
    // Category icons map
    $cat_icons = ['Water Bottle'=>'fa-bottle-water','Electronics'=>'fa-microchip','Food'=>'fa-utensils','Others'=>'fa-cube'];
    if ($products && $products->num_rows > 0):
        foreach ($products as $p):
            $stock = (int)$p['product_stock'];
            $out   = $stock <= 0;
            $low   = $stock > 0 && $stock < 10;
            $cat_icon = $cat_icons[$p['product_category']] ?? 'fa-cube';
            // stock bar: assume max 200 for display
            $bar_pct = min(100, round($stock / 200 * 100));
            $bar_col = $out ? 'hsl(0,84%,60%)' : ($low ? 'hsl(38,92%,50%)' : 'hsl(142,71%,45%)');
    ?>
        <div class="prod-card">
            <?php if($out): ?><span class="out-badge"><i class="fa fa-ban" style="font-size:9px;"></i> Out of Stock</span>
            <?php elseif($low): ?><span class="low-badge"><i class="fa fa-triangle-exclamation" style="font-size:9px;"></i> Low Stock</span>
            <?php endif; ?>

            <div class="prod-header">
                <div class="prod-icon"><i class="fa <?php echo $cat_icon; ?>"></i></div>
                <div>
                    <div class="prod-name"><?php echo htmlspecialchars($p['product_name']); ?></div>
                    <span class="prod-cat"><i class="fa fa-tag" style="font-size:9px;"></i><?php echo htmlspecialchars($p['product_category']); ?></span>
                </div>
            </div>

            <div class="prod-meta">
                <div>
                    <div class="pm-label">Unit Price</div>
                    <div class="pm-val" style="color:hsl(142,60%,35%);">₹<?php echo number_format($p['product_price'], 2); ?></div>
                </div>
                <div>
                    <div class="pm-label">Stock</div>
                    <div class="pm-val" style="color:<?php echo $out ? 'hsl(0,70%,50%)' : ($low ? 'hsl(38,75%,40%)' : 'var(--text)'); ?>;"><?php echo $stock; ?></div>
                </div>
            </div>

            <div class="stock-bar-wrap">
                <div class="stock-bar-label">
                    <span>Stock Level</span>
                    <span style="color:<?php echo $bar_col; ?>;"><?php echo $stock; ?> units</span>
                </div>
                <div class="stock-bar">
                    <div class="stock-bar-fill" style="width:<?php echo $bar_pct; ?>%; background:<?php echo $bar_col; ?>;"></div>
                </div>
            </div>

            <div class="prod-actions">
                <a href="edit_product.php?id=<?php echo $p['product_id']; ?>" class="p-btn">
                    <i class="fa fa-pen-to-square"></i> Edit
                </a>
                <a href="utils.php?delete_product=true&id=<?php echo $p['product_id']; ?>" 
                   class="p-btn del ajax-delete" 
                   data-confirm="Delete <?php echo htmlspecialchars($p['product_name']); ?>?">
                    <i class="fa fa-trash-can"></i>
                </a>
            </div>
        </div>
    <?php endforeach;
    else: ?>
        <div style="grid-column:1/-1; text-align:center; padding:64px 24px; color:var(--text-muted);">
            <i class="fa fa-boxes-stacked" style="font-size:3rem; opacity:0.2; display:block; margin-bottom:16px;"></i>
            <p style="font-weight:600; font-size:15px;">No products found</p>
            <a href="add_product.php" class="btn btn-primary btn-sm" style="margin-top:16px;"><i class="fa fa-plus"></i> Add First Product</a>
        </div>
    <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>