Hooks
=====

Here you can find all available hooks and their documentation.

:warning: Those hooks aren't registered in `$GLOBALS['TL_HOOKS']` but in `$GLOBALS['ISO_CART_BE_HOOKS']`.

### getCartLabel

Called after values to display a `tl_iso_product_collection` row in BE are calculated.

**Return value** : `array` in the following form
```php
[
	0 => 'id',
	1 => 'member',
	2 => 'total',
	3 => 'store_id',
	4 => 'cart_current_step',
	5 => 'cart_last_action',
	6 => 'cart_actions',
]
```

**Arguments**:
Name | Type | Description
--- | --- | ---
$row | `array` | The `tl_iso_product_collection` as array
$label | `string` | ?
$dc | `\Contao\DataContainer` | The `DataContainer`
$argsOriginal | `array` | The values originally calculated by Contao
$objCart | `Isotope\Model\ProductCollection\Cart` | The `Cart` object
$objDraftOrder | `Isotope\Model\ProductCollection\Order\|null` | The `DraftOrder` object, if it exists
$args | `array` | The values calculated by our bundle

**Code**:
```php
public function getCartLabel(
	array $row, 
	string $label, 
	\Contao\DataContainer $dc, 
	array $argsOriginal, 
	Isotope\Model\ProductCollection\Cart $objCart, 
	?Isotope\Model\ProductCollection\Order $objDraftOrder, 
	array $args
): array
{
	return $args;
}
```
### calculateCartCurrentStep

Called after a `tl_iso_product_collection` step has been calculated.

**Return value** : `null|string`

**Arguments**:
Name | Type | Description
--- | --- | ---
$objCart | `Isotope\Model\ProductCollection\Cart` | The `Cart` object
$objDraftOrder | `Isotope\Model\ProductCollection\Order\|null` | The `DraftOrder` object, if it exists
$step | `string\|null` | The step calculated by our bundle

**Code**:
```php
public function getCartLabel(
	Isotope\Model\ProductCollection\Cart $objCart, 
	?Isotope\Model\ProductCollection\Order $objDraftOrder,
	?string $step
): ?string
{
	return $step;
}
```