# Yahoo currency rate client

```php
use Currobber\Client\Yahoo\Client;

$Client = new Client();
var_dump($Client->get('USDRUB'));
```

```php
class Currobber\Result\PairRateData#393 (3) {
  private $pairName =>
  string(7) "USD/RUB"
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
```

```php
array(2) {
  [0] =>
  class Currobber\Result\PairRateData#466 (3) {
    private $pairName =>
    string(7) "USD/RUB"
    private $rate =>
    double(73.5245)
    private $date =>
    string(10) "2015-12-30"
  }
  [1] =>
  class Currobber\Result\PairRateData#458 (3) {
    private $pairName =>
    string(7) "EUR/RUB"
    private $rate =>
    double(80.3772)
    private $date =>
    string(10) "2015-12-30"
  }
}
```
