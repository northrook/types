# Self-validating data types for PHP

## Updating the value
Create `public function update()`, with the argument `$value`, typed to match.

This method may be used to handle additonal logic, as long as it results in a valid `$value`.

Assign the updated value using `$this->updateValue( $value )`.

The `updateValue` method revalidates the new value, unless `revalidate: false` is passed.

The method must return self.
```php
// Example:
class CustomType extends Type {

	public function update( ?string $value ): self {
		$this->updateValue( $value );

		return $this;
	}

}
```