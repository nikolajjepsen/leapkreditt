<?php
// Autoload in header
// $config config class @ header
require __DIR__ . '/app/includes/header.php';

$quote = new \App\Application\Quote;
$currentQuoteId = $quote->getCurrent();
$currentQuote = json_decode($quote->validateAndGetQuoteById($currentQuoteId));

// @codingStandardsIgnoreStart
?>
<div class="bg-shapes">
	<div class="container pb-5">
		<section class="hero mb-5">
			<div class="row">
				<div class="col-lg-6">
					<section class="inticement">
						<ul class="fa-ul">
							<li><span class="fa-li"><i class="far fa-check-circle"></i></span>Send søknad på 2 minutter</li></li>
							<li><span class="fa-li"><i class="far fa-check-circle"></i></span>Du får svar innen 1 time</li></li>
							<li><span class="fa-li"><i class="far fa-check-circle"></i></span>Det er helt gratis og uforpliktende</li></li>
						</ul>
					</section>
					<section class="text-container text-center">
						<p><?php echo ucfirst(strtolower($config->get('site:name:format'))); ?> hjelper deg med å finne akkurat det lånet som passer dine behov. Vi er alltid på din side og jobber alltid for å sikre deg det billigste lånet.</p>
					</section>
				</div>
				<div class="col-lg-6">
					<section class="intro mb-3 ml-3">
						<h1>Find lån i dag på bare 2 minutter</h1>
					</section>
					<section class="signup">
						<?php 
						if (!$currentQuote) {
							$recurringVisitor = false;
						} else {
							$recurringVisitor = true;
						}
						?>		
						<div class="card<?php echo $recurringVisitor ? ' d-none' : ''; ?>" id="application-form">
							<h3 class="mb-3 text-center">
								Velg lånebeløp
							</h3>
							<form action="#" method="POST" id="signup">
								<section class="inputs">
									<div class="slider-amount text-center">
										<span><?php echo number_format($config->get('slider:default'), 0, ',', '.'); ?></span> kr
									</div>
									<div class="range-container">
										<span class="min">
											<?php echo number_format($config->get('slider:min'), 0, ',', '.'); ?> kr
										</span>
										<span class="max">
											<?php echo number_format($config->get('slider:max'), 0, ',', '.'); ?> kr
										</span>
										<input 
											type="range" 
											min="<?php echo $config->get('slider:min'); ?>" 
											max="<?php echo $config->get('slider:max'); ?>" 
											step="<?php echo $config->get('slider:step'); ?>" 
											data-orientation="horizontal" 
											value="<?php echo $config->get('slider:default'); ?>" 
											name="slider-amount-val"
										>
									</div>

									<div class="form-label-group">
										<input type="text" name="name" placeholder="For- og etternavn" class="form-control my-3" required>
										<label for="name">For- og etternavn</label>
									</div>

									<div class="form-label-group">
									<input type="text" name="email" placeholder="E-postadresse" class="form-control mb-3" required>
										<label for="email">E-postadresse</label>
									</div>
									<div class="form-label-group">
									<input type="text" name="mobile" placeholder="Mobilnummer" class="form-control mb-3" required>
										<label for="mobile">Mobilnummer</label>
									</div>
								</section>
								<p>Some!</p>
								<section class="terms mb-5">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" id="terms-check" name="terms" required>
										<label class="form-check-label" for="terms-check">Jeg har lest og godtar <a href="#">vilkårene</a> for å bruke denne tjenesten.</label>
									</div>
								</section>

								<section class="secure">
									<button type="submit" class="btn btn-lg btn-primary">
										Send søknad 
										<i class="fas fa-arrow-alt-circle-right"></i>
									</button>
									<p class="mt-3">
										<img src="public/images/padlock.svg" width="20px" style="margin-top:-6px;" /> 
										Søknaden din sendes sikkert via en kryptert forbindelse.
									</p>
								</section>
							</form>
						</div>

						<div class="card d-none" id="confirm-identity">
							<h3 class="mb-4 text-center">Bekreft at det er deg</h3>
							<p>
								Vi ser at du allerede har søkt om lån, og vi har vurdert alternativene dine. Anbefalingene våre ligger forsatt klare til deg.
							</p>
							<p>
								Vi må imidlertid bekrefte e-posten din før du kan gå videre. Få tilsendt en bekreftelsesmail. Det kan ta et par minutter før e-posten kommer frem.
							</p>
							<section class="text-center mt-3" id="confirm-code-container">
								<a href="#" class="btn btn-lg btn-primary" id="send-confirm-mail">Send bekreftelsesmail <i class="far fa-envelope"></i></a>

								<div id="enter-code-container" class="d-none">
									<form action="" method="POST" id="code-form">
										<div class="form-label-group">
											<input type="text" name="confirmCode" placeholder="Skriv inn bekreftelseskode" class="form-control mb-3" required>
											<label for="confirmCode">Skriv inn bekreftelseskode</label>
										</div>
										<button type="submit" class="btn btn-lg btn-primary" id="confirm-code-btn">Bekreft og se anbefalinger</button>
									</form>
									<p class="mt-2">Har du ikke mottatt bekreftelseskoden? <a href="#" class="" id="resend-confirm-code">Send koden på nytt</a>.</p>
								</div>
							</section>
							<p class="mt-4">
								Har du behov for å starte forfra? Ikke noe problem. Husk at e-post og telefonnummer ikke kan brukes mer enn én gang.
							</p>
							<p class="text-center mt-2">
								<a href="#" class="btn btn-secondary back-to-application">Bruk andre opplysninger</a>
							</p>
							<section class="secure mt-1">
								<p class="mt-3">
									<img src="public/images/padlock.svg" width="20px" style="margin-top:-6px;"> 
									Som altid sendes all trafik via en kryptert forbindelse.
								</p>
							</section>
						</div>

						<div class="card<?php echo !$recurringVisitor ? ' d-none' : ''; ?>" id="recurring-visitor">
							<h3 class="mb-4 text-center">Velkommen tilbake<?php echo isset($currentQuote->firstname) ? ', ' . $currentQuote->firstname : ''; ?></h3>
							<p>
								Hyggelig å se deg igjen. Vi ser at du allerede har søkt om lån, og vi har vurdert alternativene dine.
								Anbefalingene våre ligger fortsatt klare til deg.
							</p>
							<p>
								Har behovene dine endret seg siden sist? Du kan når som helst gå gjennom opplysninger og valgte lånebeløp.
							</p>
							<p class="text-center mt-3">
								<a href="quote.php" class="btn btn-lg btn-primary">Se anbefalinger <i class="fas fa-arrow-alt-circle-right"></i></a>
							</p>
							<p class="mt-4">
							Har du behov for å opprette en ny søknad? Ikke noe problem. Men vær oppmerksom på at e-post og mobilnummer ikke kan brukes på mer enn en søknad.
							</p>
							<p class="text-center mt-2">
								<a href="#" class="btn btn-secondary back-to-application">Oppret ny søknad</a>
							</p>

							<section class="secure mt-1">
								<p class="mt-3">
									<img src="public/images/padlock.svg" width="20px" style="margin-top:-6px;"> 
									Som altid sendes all trafik via en kryptert forbindelse.
								</p>
							</section>
						</div>
					</section>
				</div>	
			</div>
		</section>
	</div>
</div>
<div class="bg-white">
	<div class="container">
		<section class="primary">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class="title">Fra søknad til utbetaling</h2>
				</div>
				<div class="col-md-4 px-4 pt-2">
					<div class="card">
						<img class="card-img-top" src="public/images/primary-fill.svg" alt="">
						<div class="card-body">
							<h4 class="card-title">Send søknaden din</h4>
							<p class="card-text">Fyll ut skjemaet og trykk på knappen. Opplysningene dine brukes bare til å gjennomgå lånemulighetene dine samt kontakte deg senere.</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 px-4 pt-2">
					<div class="card">
						<img class="card-img-top" src="public/images/primary-work.svg" alt="">
						<div class="card-body">
							<h4 class="card-title">Vi ser på mulighetene</h4>
							<p class="card-text">Vi gjennomgår lånemulighetene dine og finner opptil tre lån som passer best med behovene dine. Du får svar umiddelbart!</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 px-4 pt-2">
					<div class="card">
						<img class="card-img-top" src="public/images/primary-winner.svg" alt="">
						<div class="card-body">
							<h4 class="card-title">Utbetaling av lånet</h4>
							<p class="card-text">Lånet utbetales ofte samme dagen som du underskriver på det. Et billigere lån gir deg muligheten til å spare opp til drømmene dine.</p>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="/public/scripts/rangeslider.min.js"></script>
<script src="/public/scripts/signup.js"></script>
<?php
require __DIR__ . '/app/includes/footer.php';
?>