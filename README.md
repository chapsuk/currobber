# Yahoo currency rate client

```php
use Currobber\Client\Yahoo\Client;

$Client = new Client();
var_dump($Client->get('USDRUB'));

=================

class Currobber\Result\PairRateData#393 (3) {
  private $pairName =>
  string(7) "USDRUB"
  private $rate =>
  double(73.5245)
  private $date =>
  string(10) "2015-12-30"
}
```

```php
use Currobber\Client\Yahoo\Client;

$Client = new Client();
var_dump($Client->getMulti(['USDRUB', 'EURRUB']));

=================

array(2) {
  [0] =>
  class Currobber\Result\PairRateData#466 (3) {
    private $pairName =>
    string(7) "USDRUB"
    private $rate =>
    double(73.5245)
    private $date =>
    string(10) "2015-12-30"
  }
  [1] =>
  class Currobber\Result\PairRateData#458 (3) {
    private $pairName =>
    string(7) "EURRUB"
    private $rate =>
    double(80.3772)
    private $date =>
    string(10) "2015-12-30"
  }
}
```

# Currencylayer api client

Free subscription support source USD only

```php
use Currobber\Client\Currencylayer\Client;

$accessKey = '<YOUR_API_KEY>';
$Client = new Client($accessKey);
var_dump($Client->get('USD', 'RUB', '2015-10-10'));

=================

class Currobber\Result\PairQuoteData#67 (3) {
  private $pairName =>
  string(6) "USDRUB"
  private $quote =>
  double(61.775002)
  private $date =>
  string(10) "2015-10-10"
}
```

```php
use Currobber\Client\Currencylayer\Client;

$accessKey = '<YOUR_API_KEY>';
$Client = new Client($accessKey);
var_dump($Client->getMulti('USD', ['EUR', 'RUB'], '2015-10-10'));

=================

array(2) {
  [0] =>
  class Currobber\Result\PairQuoteData#67 (3) {
    private $pairName =>
    string(6) "USDEUR"
    private $quote =>
    double(0.880398)
    private $date =>
    string(10) "2015-10-10"
  }
  [1] =>
  class Currobber\Result\PairQuoteData#65 (3) {
    private $pairName =>
    string(6) "USDRUB"
    private $quote =>
    double(61.775002)
    private $date =>
    string(10) "2015-10-10"
  }
}
```
