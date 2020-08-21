<?php
session_start();
unset($_SESSION['loans']);
require 'vendor/autoload.php';

$config  = new \App\Application\Config;
$db      = \App\Database\Instance::get();
$api     = new \App\Api\Api;
$quote   = new \App\Application\Quote;
$request = $api->request();

$currentQuoteId = $quote->getCurrent();

if (!$currentQuote = $quote->validateAndGetQuoteById($currentQuoteId)) {
    echo 'Couldn\'t find active quote .. redirect should happen here with error.';
    die;
}
$currentQuote = json_decode($currentQuote);

// Get loans as arrays
if (isset($_SESSION['loans']) && json_decode($_SESSION['loans']) && count(json_decode($_SESSION['loans'])) >= 1) {
    $suggestions = json_decode($_SESSION['loans'], true);
}elseif ($grabLoans = $quote->getSuggestedLoansByQuoteId($currentQuoteId, 'epc', 'DESC', 3)) {
    $suggestions = $grabLoans;
} else {
    // TODO: Generate random set of loans by static arrays w/o direct tracking.
    // Most likely issue is missing connection to API, thus no tracking available.
}
// Type catered -> suggestions based on user data
// Type general -> suggestions based on country. user data not valid for list.
$suggestionType = $suggestions['content'];
$loans = $suggestions['loans'];

require __DIR__ . '/app/includes/header.php';
// @codingStandardsIgnoreStart
?>
<div class="bg-white">
    <div class="bg-white recommendations" id="recommendations">
        <div class="bg-white">
            <div class="container">
                <section>
                    <div class="row">
                        <div class="col-lg-12 text-center text-lg-left">
                            <h2 class="title mb-5">
                                <i class="far fa-star color-purple d-none d-md-inline-block"></i> Her er våre anbefalinger til deg
                                <?php echo $currentQuote->firstname ?? ''; ?>
                            </h2>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="container">
            <section class="comparison-box">
                <section class="header bg-purple color-white">
                    <div class="inner">
                        <div class="row justify-content-between">
                            <div class="col-md-5">
                                <p>
                                    <b>Det beste vi kunne finne</b><br />
                                    Anbefalingene våre er basert på opplysningene dine. Du kan få mer nøyaktige 
                                    anbefalinger ved å fylle ut litt flere opplysninger nedenfor.
                                </p>
                            </div>
                            <div class="col-md-4 text-left text-md-right">
                                <span>
                                    Lånebeløpet ditt: <h4><?php echo number_format($currentQuote->loan_amount, 0, ',', '.'); ?> kr</h4>
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="contents bg-white">
                    <div class="inner">
                        <div class="row comparison">
                            <?php 
                            $i = 0;
                            foreach ($loans as $loan) { 
                            ?>
                            <div class="col-md-12 px-4 pt-2">
                                <div class="loan-container bg-white<?php echo $i == 0 ? ' first-element"' : ''; ?>">
                                    <div class="loan-body">
                                        <div class="inner">
                                            <div class="row">
                                                <div class="col-md-6 col-lg-2 icon text-center text-lg-left">
                                                    <img src="<?php echo $config->get('site:images:url'); ?>/<?php echo $loan['image_name']; ?>">
                                                </div>
                                                <div class="col-md-6 col-lg-2 property">
                                                    <span class="header">Lånebeløp</span>
                                                    <span class="value"><?php echo number_format($loan['min_amount'], 0, '.', ' '); ?>
                                                        -
                                                        <?php echo number_format($loan['max_amount'], 0, '.', ' '); ?></span>
                                                </div>
                                                <div class="col-md-6 col-lg-2 property">
                                                    <span class="header">ÅOP</span>
                                                    <span class="value"><?php echo $loan['yearly_cost_percent']; ?></span>
                                                </div>
                                                <div class="col-md-6 col-lg-2 property">
                                                    <span class="header">Løpetid</span>
                                                    <span class="value"><?php echo $loan['min_tenure']; ?> -
                                                        <?php echo $loan['max_tenure']; ?> måneder</span>
                                                </div>
                                                <div class="col-md-6 col-lg-2 property d-block d-md-none d-lg-block">
                                                    <span class="header">Min. alder</span>
                                                    <span class="value"><?php echo $loan['min_age']; ?> år</span>
                                                </div>
                                                <div class="col-md-12 col-lg-2 text-center text-lg-right">
                                                    <a href="/recommendation/<?php echo $loan['id']; ?>/" class="btn btn-lg btn-success px-5 px-lg-2">Få tilbud</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row secondary">
                                            <div class="col-md-4">
                                                <p><i class="fas fa-check color-purple"></i> Skriv under med NEM-ID</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><i class="fas fa-check color-purple"></i> Få straks svar på ansøgning</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><i class="fas fa-check color-purple"></i> Udbetaling senest dagen efter</p>
                                            </div>
                                        </div>
                                        <div class="row secondary">
                                            <div class="col-12">
                                                <span class="terms">100.000 kr. over 5 år. Variabel debitorrente 6,99% -
                                                    22,99%. ÅOP 7,64 % til 25,98 %. Mdl. ydelse 1.980 - 2.819 kr.
                                                    Kreditomkostninger 19.940 til 70.407 kr. Tilbagebetaling 119.940 til
                                                    170.407 kr.. Maks ÅOP 25,98%.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            $i++;
                            }
                            ?>
                        </div>
                    </div>
                </section>
            </section>
        </div>
    </div>

    <div class="bg-white settings" id="quote-settings">
        <div class="container">
            <section class="compare">
                <div class="row">
                    <div class="col-lg-10 mx-auto text-center text-lg-left">
                        <h2 class="title mb-5">
                            <i class="fas fa-users-cog color-purple d-none d-lg-inline-block"></i> Endre opplysningene dine
                        </h2>
                    </div>
                </div>
                <form>
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="card">
                                <div class="card-header py-4">
                                    <h4>Lån- og kontaktopplysninger</h4>
                                </div>
                                <div class="card-body pt-4">
                                    <div class="row mb-4">
                                        <div class="col-md-8 offset-md-2 inputs">
                                            <div class="slider-amount text-center">
                                                <span><?php echo number_format($currentQuote->loan_amount, 0, ',', '.'); ?></span> kr
                                            </div>
                                            <div class="range-container">
                                                <span class="min"><?php echo number_format($config->get('slider:min'), 0, ',', '.'); ?> kr</span>
                                                <span class="max"><?php echo number_format($config->get('slider:max'), 0, ',', '.'); ?> kr</span>
                                                <input 
                                                    type="range" 
                                                    min="<?php echo $config->get('slider:min'); ?>" 
                                                    max="<?php echo $config->get('slider:max'); ?>" 
                                                    step="<?php echo $config->get('slider:step'); ?>" 
                                                    data-orientation="horizontal" 
                                                    value="<?php echo $currentQuote->loan_amount; ?>" 
                                                    name="slider-amount-val"
                                                >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-label-group">
                                                <input type="number" name="tenure" placeholder="Løpetid (måneder)" value="<?php echo $currentQuote->tenure ?? ''; ?>"class="form-control mt-2" id="">
                                                <label for="nameField">Løpetid (måneder)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-label-group">
                                                <input type="number" name="age" placeholder="Alder" class="form-control mt-2" value="<?php echo $currentQuote->age ?? ''; ?>" id="">
                                                <label for="nameField">Alder</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-label-group">
                                                <input type="text" name="firstname" placeholder="Fornavn" value="<?php echo $currentQuote->firstname; ?>" class="form-control mt-4 mt-md-4" id="" required>
                                                <label for="nameField">Fornavn</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        <div class="form-label-group">
                                                <input type="text" name="lastname" placeholder="Efternavn" value="<?php echo $currentQuote->lastname; ?>" class="form-control mt-2 mt-md-4" id="" required>
                                                <label for="nameField">Etternavn</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-label-group">
                                                <input type="text" name="email" placeholder="E-postadresse" value="<?php echo $currentQuote->email ?? ''; ?>" class="form-control mt-4 mt-md-3" id="" required>
                                                <label for="nameField">E-postadresse</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-label-group">
                                                <input type="text" name="mobile" placeholder="Mobilnummer" value="<?php echo $currentQuote->mobile ?? ''; ?>" class="form-control mt-2 mt-md-3" id="" required>
                                                <label for="nameField">Mobilnummer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <div class="form-label-group">
                                                <input type="hidden" value="<?php echo $currentQuoteId; ?>" name="quoteId" />
                                                <button type="submit" class="btn btn-lg btn-success">Oppdater oplysninger</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>



<div class="bg-white" id="page-selection">
    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-4 col-md-4 col-lg-2">
                <span class="page" id="select-recommendations" data-page="recommendations">
                    <i class="far fa-star"></i><br />
                    Anbefalinger
                </span>
            </div>
            <div class="col-4 col-md-4 col-lg-2">
                <span class="page" id="select-quote-settings" data-page="settings">
                    <i class="fas fa-users-cog"></i><br />
                    Opplysningene dine
                </span>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="/public/scripts/rangeslider.min.js"></script>
<script src="/public/scripts/quote.js"></script>

<?php
require __DIR__ . '/app/includes/footer.php';
?>