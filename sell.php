<?php
    session_start();

    include('connection.php');
    include('utils.php');
    include 'includes/auth_validate.php';

    try {
        if(isset($_GET['sell'])){
            if(!isset($_GET['product_id']) || empty($_GET['product_id'])){
                throw new Exception("Please select at least one product.");
            }
            
            $selected_product_ids = $_GET['product_id'];
            $all_quantities = $_GET['product_quantity'];
            
            foreach($selected_product_ids as $pid) {
                $quantity = isset($all_quantities[$pid]) ? $all_quantities[$pid] : "";
                if($quantity === "" || $quantity <= 0) {
                    throw new Exception("Please enter a valid quantity for all selected products.");
                }
            }

            $sql = "INSERT INTO orders (type, customer_id, total, payment_status, date) VALUES (1, ?, 0, ?, CURRENT_TIMESTAMP)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("is", $_GET['id'], $_GET['payment_status2']);
            $stmt->execute();

            $order_id = $con->insert_id;
            $total = 0;
            
            foreach($selected_product_ids as $pid) {
                $pid = (int)$pid;
                $quantity = (int)$all_quantities[$pid];
                
                $price_row = $con->query("SELECT product_price, product_stock FROM products WHERE product_id = $pid")->fetch_assoc();
                $product_price = $price_row['product_price'];
                $product_stock = $price_row['product_stock'];
                
                if($quantity > $product_stock) {
                    throw new Exception("Insufficient stock for product ID: $pid");
                }
                
                $subtotal = (float)$product_price * $quantity;
                $total += $subtotal;

                $stmt = $con->prepare("INSERT INTO orders_product (order_id, product_id, quantity) VALUES (?,?,?)");
                $stmt->bind_param("iii", $order_id, $pid, $quantity);
                $stmt->execute();
                
                $con->query("UPDATE products SET product_stock = product_stock - $quantity WHERE product_id = $pid");
            }
            
            $con->query("UPDATE orders SET total = $total WHERE order_id = $order_id");

            redirect("bill.php?order_id=".$order_id);
            exit;
        }
    } catch(Exception $e) {
        $_SESSION['failure'] = $e->getMessage();
    }

    if(!isset($_GET['id'])) {
        header("Location: customer.php");
        exit;
    }

    $id = (int)$_GET['id'];
    $result = $con->query("SELECT * FROM customer WHERE customer_id = $id");
    $customer = $result->fetch_assoc();
    
    $result2 = $con->query("SELECT * FROM products WHERE product_stock > 0 ORDER BY product_name ASC");
    $out_of_stock = $con->query("SELECT COUNT(*) as cnt FROM products WHERE product_stock <= 0")->fetch_assoc()['cnt'];
    
    include_once('includes/header.php'); 
?>

<style>
.sell-layout {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 20px;
    align-items: start;
}
@media(max-width:900px) { .sell-layout { grid-template-columns: 1fr; } }

.sell-sidebar {
    position: sticky;
    top: calc(var(--topbar-h) + 24px);
    display: flex; flex-direction: column; gap: 16px;
}

.info-block {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
}
.info-block-header {
    padding: 14px 20px; border-bottom: 1px solid var(--border);
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-muted);
    background: var(--bg2);
}
.info-block-body { padding: 20px; }

.customer-pill {
    display: flex; align-items: center; gap: 14px;
}
.customer-pill-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 18px; font-weight: 800; flex-shrink: 0;
}

.payment-selector { display: flex; gap: 10px; }
.pay-option {
    flex: 1; padding: 12px; border-radius: 12px; border: 2px solid var(--border);
    background: var(--surface-solid); cursor: pointer; text-align: center;
    font-weight: 700; font-size: 13px; transition: all 0.2s ease;
    color: var(--text-muted); display: flex; align-items: center; justify-content: center; gap: 8px;
}
.pay-option input { display: none; }
.pay-option.active-pending { border-color: hsl(38,92%,50%); background: hsla(38,92%,50%,0.1); color: hsl(38,75%,40%); }
.pay-option.active-paid    { border-color: hsl(142,71%,45%); background: hsla(142,71%,45%,0.1); color: hsl(142,60%,35%); }
.dark .pay-option.active-pending { color: hsl(38,92%,62%); }
.dark .pay-option.active-paid    { color: hsl(142,71%,58%); }

/* Order summary */
.order-summary-lines { list-style:none; padding:0; margin:0; }
.order-summary-lines li {
    display:flex; justify-content:space-between; align-items:center;
    padding:8px 0; border-bottom:1px solid var(--border); font-size:13px;
}
.order-summary-lines li:last-child { border-bottom:none; }
.osl-name { font-weight:700; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.osl-sub  { font-size:11px; color:var(--text-muted); margin-top:1px; }
.osl-total{ font-weight:800; color:var(--text); }

.grand-total-row {
    margin-top:14px; padding:14px 16px; border-radius:12px;
    background: linear-gradient(135deg, hsl(224,50%,8%), hsl(235,45%,14%));
    display:flex; justify-content:space-between; align-items:center;
}
.gt-label { color:hsla(210,40%,98%,0.6); font-size:12px; font-weight:600; }
.gt-val   { color:white; font-size:22px; font-weight:800; letter-spacing:-0.03em; }

.submit-btn {
    width:100%; padding:16px !important; font-size:16px !important; font-weight:800 !important;
    border-radius:14px !important;
}

/* Product table */
.product-table-wrap {
    background:var(--surface); border:1px solid var(--border); border-radius:16px; overflow:hidden;
}
.pt-header {
    padding:16px 20px; border-bottom:1px solid var(--border);
    display:flex; justify-content:space-between; align-items:center;
    background:var(--bg2);
}
.pt-search { position:relative; }
.pt-search i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px; pointer-events:none; }
.pt-search input { padding-left:36px !important; width:220px; }

.prod-row { transition: background 0.15s ease; }
.prod-row.selected td { background: hsla(var(--primary-raw),0.04) !important; }

.product-checkbox {
    width: 20px; height: 20px; cursor: pointer; accent-color: var(--primary);
    border-radius: 4px;
}

.qty-input {
    width: 90px !important; height: 36px !important; padding: 4px 10px !important;
    border-radius: 8px !important; font-weight: 700 !important; font-size: 14px !important;
    text-align: center;
}
.qty-input:disabled { opacity:0.35; }

.stock-pill {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700;
}
.stock-ok  { background:hsla(142,71%,45%,0.1); color:hsl(142,60%,35%); }
.stock-low { background:hsla(38,92%,50%,0.1); color:hsl(38,75%,40%); }
.dark .stock-ok  { color:hsl(142,71%,58%); }
.dark .stock-low { color:hsl(38,92%,62%); }

.no-products-note {
    padding:12px 20px; font-size:12.5px; color:var(--text-muted); 
    border-top:1px solid var(--border); background:hsla(0,84%,60%,0.04);
    display:flex; align-items:center; gap:8px;
}
</style>

<div id="page-wrapper">
    <?php include 'includes/flash_messages.php'; ?>

    <h1 class="page-header animate-fade-in">
        New Sale
        <a href="customer.php" class="btn btn-sm" style="background:var(--bg2); border:1px solid var(--border) !important; color:var(--text-muted); font-size:13px;">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </h1>

    <form action="sell.php" method="GET" id="sell-form">
        <input type="hidden" name="id" value="<?php echo $customer['customer_id']; ?>">

        <div class="sell-layout animate-fade-in">

            <!-- ─── Left sidebar ─── -->
            <div class="sell-sidebar">

                <!-- Customer info -->
                <div class="info-block">
                    <div class="info-block-header"><i class="fa fa-user"></i> Customer</div>
                    <div class="info-block-body">
                        <div class="customer-pill">
                            <div class="customer-pill-avatar"><?php echo strtoupper(substr($customer['customer_name'],0,1)); ?></div>
                            <div>
                                <div style="font-weight:800; font-size:16px;"><?php echo htmlspecialchars($customer['customer_name']); ?></div>
                                <div style="font-size:12px; color:var(--text-muted); margin-top:3px;">
                                    <i class="fa fa-phone" style="font-size:10px; color:var(--primary);"></i>
                                    <?php echo htmlspecialchars($customer['customer_phone']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment status -->
                <div class="info-block">
                    <div class="info-block-header"><i class="fa fa-credit-card"></i> Payment Status</div>
                    <div class="info-block-body">
                        <div class="payment-selector">
                            <label class="pay-option active-pending" id="lbl-pending" onclick="selectStatus('pending')">
                                <input type="radio" name="payment_status2" value="pending" id="r-pending" checked>
                                <i class="fa fa-clock"></i> Pending
                            </label>
                            <label class="pay-option" id="lbl-paid" onclick="selectStatus('paid')">
                                <input type="radio" name="payment_status2" value="paid" id="r-paid">
                                <i class="fa fa-circle-check"></i> Paid
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Live Order Summary -->
                <div class="info-block">
                    <div class="info-block-header"><i class="fa fa-list-check"></i> Order Summary</div>
                    <div class="info-block-body">
                        <ul class="order-summary-lines" id="summary-list">
                            <!-- Items will be injected here -->
                        </ul>
                        <div id="empty-summary" style="display:flex; justify-content:center; color:var(--text-muted); font-size:13px; padding:20px 0;">
                            <span>No items selected yet</span>
                        </div>
                        <div class="grand-total-row" id="grand-total-row" style="display:none;">
                            <span class="gt-label">Grand Total</span>
                            <span class="gt-val" id="SALE_GRAND_TOTAL_VALUE">₹0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" name="sell" value="add_customer" class="btn btn-primary submit-btn" id="submit-btn" disabled>
                    Complete Sale <i class="fa fa-arrow-right" style="margin-left:8px;"></i>
                </button>
            </div>

            <!-- ─── Product Selection ─── -->
            <div class="product-table-wrap">
                <div class="pt-header">
                    <div>
                        <div style="font-weight:800; font-size:15px;">Select Products</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                            <?php echo $result2->num_rows; ?> products available
                            <?php if($out_of_stock > 0): ?>
                            · <span style="color:hsl(0,70%,50%);"><?php echo $out_of_stock; ?> out of stock (hidden)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pt-search">
                        <i class="fa fa-search"></i>
                        <input type="text" class="form-control" id="prod-search" placeholder="Filter products..." oninput="filterProducts(this.value)">
                    </div>
                </div>

                <div class="table-responsive" style="border-radius:0; border:none; box-shadow:none;">
                    <table class="table table-hover" style="margin:0;">
                        <thead>
                            <tr>
                                <th width="50" class="text-center"></th>
                                <th>Product</th>
                                <th width="130">Price</th>
                                <th width="110">Stock</th>
                                <th width="140">Quantity</th>
                                <th width="130" class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="product-tbody">
                        <?php while($p = $result2->fetch_assoc()):
                            $stock = (int)$p['product_stock'];
                            $is_low = $stock < 10;
                        ?>
                        <tr class="prod-row" 
                            data-pid="<?php echo $p['product_id']; ?>"
                            data-name="<?php echo htmlspecialchars($p['product_name']); ?>"
                            data-price="<?php echo $p['product_price']; ?>"
                            data-stock="<?php echo $stock; ?>"
                            data-search="<?php echo strtolower($p['product_name'].' '.$p['product_category']); ?>">
                            <td class="text-center">
                                <input type="checkbox" name="product_id[]" 
                                       value="<?php echo $p['product_id']; ?>"
                                       class="product-checkbox"
                                       data-pid="<?php echo $p['product_id']; ?>"
                                       onchange="onProductToggle(this)">
                            </td>
                            <td>
                                <div style="font-weight:700;"><?php echo htmlspecialchars($p['product_name']); ?></div>
                                <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.04em; margin-top:2px;"><?php echo htmlspecialchars($p['product_category']); ?></div>
                            </td>
                            <td>
                                <span style="font-weight:700; color:hsl(142,60%,35%);">₹<?php echo number_format($p['product_price'],2); ?></span>
                            </td>
                            <td>
                                <span class="stock-pill <?php echo $is_low ? 'stock-low' : 'stock-ok'; ?>">
                                    <?php if($is_low): ?><i class="fa fa-triangle-exclamation" style="font-size:9px;"></i><?php endif; ?>
                                    <?php echo $stock; ?>
                                </span>
                            </td>
                            <td>
                                <input type="number" 
                                       id="qty-<?php echo $p['product_id']; ?>"
                                       name="product_quantity[<?php echo $p['product_id']; ?>]"
                                       class="form-control qty-input"
                                       min="1" max="<?php echo $stock; ?>"
                                       placeholder="0"
                                       disabled
                                       oninput="onQtyChange(<?php echo $p['product_id']; ?>, <?php echo $p['product_price']; ?>, this.value, '<?php echo htmlspecialchars($p['product_name']); ?>')">
                            </td>
                            <td class="text-right">
                                <span id="sub-<?php echo $p['product_id']; ?>" style="font-weight:800; font-size:15px; color:var(--text);">—</span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($out_of_stock > 0): ?>
                <div class="no-products-note">
                    <i class="fa fa-circle-info" style="color:var(--text-muted);"></i>
                    <?php echo $out_of_stock; ?> product(s) with zero stock are hidden. <a href="product.php" style="color:var(--primary); font-weight:700; margin-left:4px;">Restock →</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    // ─── Payment Toggle ───────────────────────────────
    // ─── Payment Toggle ───────────────────────────────
    function selectStatus(status) {
        const lp = document.getElementById('lbl-pending');
        const lc = document.getElementById('lbl-paid');
        const rp = document.getElementById('r-pending');
        const rc = document.getElementById('r-paid');
        
        if (status === 'paid') {
            lp.classList.remove('active-pending');
            lc.classList.add('active-paid');
            if (rc) rc.checked = true;
        } else {
            lp.classList.add('active-pending');
            lc.classList.remove('active-paid');
            if (rp) rp.checked = true;
        }
    }

    // ─── Event Handlers ───────────────────────────────
    function onProductToggle(checkbox) {
        const pid = checkbox.dataset.pid;
        const row = checkbox.closest('tr');
        const qtyInp = document.getElementById('qty-' + pid);

        if (checkbox.checked) {
            if (qtyInp) {
                qtyInp.disabled = false;
                if (!qtyInp.value || parseInt(qtyInp.value) <= 0) {
                    qtyInp.value = 1;
                }
            }
            if (row) row.classList.add('selected');
        } else {
            if (qtyInp) qtyInp.disabled = true;
            if (row) row.classList.remove('selected');
        }
        updateSummary();
    }

    function onQtyChange() {
        updateSummary();
    }

    // ─── Core Logic (Defensive & Robust) ───────────────
    function updateSaleSummary() {
        console.log("SALE_DEBUG: Executing updateSaleSummary");
        
        const summaryListContainer = document.getElementById('summary-list');
        const emptyStateDiv        = document.getElementById('empty-summary');
        const totalLineRow         = document.getElementById('grand-total-row');
        const totalValueSpan       = document.getElementById('SALE_GRAND_TOTAL_VALUE');
        const mainSubmitBtn        = document.getElementById('submit-btn');

        if (!summaryListContainer || !totalValueSpan) {
            console.error("SALE_DEBUG: Critical DOM elements missing.");
            return;
        }

        let calculatedGrandTotal = 0;
        let selectedItemCount = 0;
        let listItemsHtml = '';

        // Select all checked products accurately
        const activeCheckboxes = document.querySelectorAll('.product-checkbox:checked');
        
        activeCheckboxes.forEach(cb => {
            try {
                const pid = cb.dataset.pid;
                const row = cb.closest('tr');
                if (!row) return;

                const name = row.dataset.name || "Unknown Item";
                const price = parseFloat(row.dataset.price) || 0;
                
                const qtyInput = document.getElementById('qty-' + pid);
                const quantity = qtyInput ? (parseFloat(qtyInput.value) || 0) : 0;
                
                const rowSubtotalDisplay = document.getElementById('sub-' + pid);
                
                if (quantity > 0) {
                    const itemSubtotal = price * quantity;
                    calculatedGrandTotal += itemSubtotal;
                    selectedItemCount++;

                    // Update Table Row Subtotal
                    if (rowSubtotalDisplay) {
                        rowSubtotalDisplay.textContent = '₹' + itemSubtotal.toFixed(2);
                    }

                    // Build Sidebar Summary
                    listItemsHtml += `
                        <li style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid var(--border);">
                            <div style="flex:1; overflow:hidden;">
                                <div style="font-weight:700; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${name}</div>
                                <div style="font-size:11px; color:var(--text-muted);">${quantity} × ₹${price.toFixed(2)}</div>
                            </div>
                            <div style="font-weight:800; color:var(--text); margin-left:12px;">₹${itemSubtotal.toFixed(2)}</div>
                        </li>
                    `;
                } else {
                    if (rowSubtotalDisplay) rowSubtotalDisplay.textContent = '—';
                }
            } catch (err) {
                console.error("SALE_DEBUG: Error processing item:", err);
            }
        });

        // Sync with DOM
        summaryListContainer.innerHTML = listItemsHtml;

        if (selectedItemCount === 0) {
            if (emptyStateDiv) emptyStateDiv.style.display = 'flex';
            if (totalLineRow)  totalLineRow.style.display = 'none';
            if (mainSubmitBtn) mainSubmitBtn.disabled = true;
            totalValueSpan.textContent = '₹0.00';
        } else {
            if (emptyStateDiv) emptyStateDiv.style.display = 'none';
            if (totalLineRow)  totalLineRow.style.display = 'flex';
            
            const displayTotal = '₹' + calculatedGrandTotal.toFixed(2);
            console.log("SALE_DEBUG: New Grand Total:", displayTotal);
            totalValueSpan.textContent = displayTotal;
            
            if (mainSubmitBtn) mainSubmitBtn.disabled = false;
        }
    }

    // Filter Logic
    function filterProducts(val) {
        const query = val.toLowerCase();
        document.querySelectorAll('#product-tbody tr.prod-row').forEach(row => {
            const text = row.dataset.search || "";
            row.style.display = text.includes(query) ? "" : "none";
        });
    }

    // Global wrappers for HTML event hooks
    window.onProductToggle = onProductToggle;
    window.onQtyChange = updateSaleSummary;
    window.updateSummary = updateSaleSummary;

    // Initialize
    document.addEventListener('DOMContentLoaded', updateSaleSummary);
</script>

<?php include 'includes/footer.php'; ?>