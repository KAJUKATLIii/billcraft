<?php
session_start();
include_once('includes/header.php');

// Game state variables
$location = isset($_SESSION['location']) ? $_SESSION['location'] : 'start';

// Game logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($location) {
        case 'start':
            if ($action == 'explore') $location = 'cave';
            break;
        case 'cave':
            if ($action == 'back') $location = 'start';
            elseif ($action == 'open_chest') $location = 'treasure';
            break;
        case 'treasure':
            if ($action == 'continue') $location = 'start';
            break;
    }
    $_SESSION['location'] = $location;
}
?>

<div id="page-wrapper">
    <div class="row animate-fade-in" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
        <div class="col-lg-6 col-md-8 text-center">
            <div style="font-size: 120px; font-weight: 900; opacity: 0.05; position: absolute; top: -40px; left: 50%; transform: translateX(-50%); z-index: -1;">404</div>
            
            <div class="card-modern" style="padding: 48px; border-top: 4px solid hsl(var(--primary));">
                <div style="font-size: 48px; color: hsl(var(--primary)); margin-bottom: 16px;">
                    <i class="fa fa-compass"></i>
                </div>
                <h2 style="font-weight: 800; margin-bottom: 24px;">Lost in the Pipeline?</h2>
                <p style="opacity: 0.6; margin-bottom: 32px;">The page you're looking for has flowed downstream. While we wait for it to return, why not try this mini-game?</p>
                
                <div style="background: hsla(var(--primary), 0.03); border: 1px solid hsla(var(--primary), 0.1); border-radius: 20px; padding: 32px; margin-bottom: 32px; text-align: left;">
                    <h5 style="text-transform: uppercase; font-size: 11px; letter-spacing: 0.1em; margin-bottom: 16px; color: hsl(var(--primary)); font-weight: 800;">Current Mission</h5>
                    
                    <div style="font-size: 16px; font-weight: 600; line-height: 1.6; margin-bottom: 24px;">
                        <?php 
                        switch ($location) {
                            case 'start': echo "You are standing in front of a mysterious cave. A strange echo comes from within."; break;
                            case 'cave': echo "You have entered the cave. In the dim light, you spot a heavy, iron-bound chest."; break;
                            case 'treasure': echo "The lid creaks open... GOLD! You've found the legendary stash!"; break;
                        }
                        ?>
                    </div>

                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <?php if ($location == 'start'): ?>
                            <form method="post"><input type="hidden" name="action" value="explore">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 24px !important;">Explore the Cave <i class="fa fa-arrow-right" style="margin-left: 8px;"></i></button>
                            </form>
                        <?php elseif ($location == 'cave'): ?>
                            <form method="post"><input type="hidden" name="action" value="back">
                                <button type="submit" class="btn btn-secondary">Go Back</button>
                            </form>
                            <form method="post"><input type="hidden" name="action" value="open_chest">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 24px !important;">Open the Chest <i class="fa fa-key" style="margin-left: 8px;"></i></button>
                            </form>
                        <?php elseif ($location == 'treasure'): ?>
                            <form method="post"><input type="hidden" name="action" value="continue">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 24px !important; background-color: hsl(142 71% 45%) !important;">Collect & Continue <i class="fa fa-coins" style="margin-left: 8px;"></i></button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="index.php" class="btn btn-secondary">
                    <i class="fa fa-house" style="margin-right: 8px;"></i> Take me home
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once('includes/footer.php'); ?>