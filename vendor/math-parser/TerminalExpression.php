<?php

//namespace PHPMathParser;

abstract class AIOW_TerminalExpression {

	protected $value = '';

	public function __construct($value) {
		$this->value = $value;
	}

	public static function factory($value) {
		if (is_object($value) && $value instanceof \AIOW_TerminalExpression) {
			return $value;
		} elseif (is_numeric($value)) {
			return new AIOW_Number($value);
		} elseif ($value == '+') {
			return new AIOW_Addition($value);
		} elseif ($value == '-') {
			return new AIOW_Subtraction($value);
		} elseif ($value == '*') {
			return new AIOW_Multiplication($value);
		} elseif ($value == '/') {
			return new AIOW_Division($value);
		} elseif (in_array($value, array('(', ')'))) {
			return new AIOW_Parenthesis($value);
		} elseif ($value == '^') {
			return new AIOW_Power($value);
		}
		throw new Exception('Undefined Value ' . $value);
	}

	abstract public function operate(AIOW_Stack $stack);

	public function isOperator() {
		return false;
	}

	public function isParenthesis() {
		return false;
	}

	public function isNoOp() {
		return false;
	}

	public function render() {
		return $this->value;
	}
}
