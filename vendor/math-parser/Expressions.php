<?php

//namespace PHPMathParser;

class AIOW_Parenthesis extends \AIOW_TerminalExpression {

	protected $precedence = 6;

	public function operate(AIOW_Stack $stack) {
	}

	public function getPrecedence() {
		return $this->precedence;
	}

	public function isNoOp() {
		return true;
	}

	public function isParenthesis() {
		return true;
	}

	public function isOpen() {
		return $this->value == '(';
	}

}

class AIOW_Number extends \AIOW_TerminalExpression {

	public function operate(AIOW_Stack $stack) {
		return $this->value;
	}

}

abstract class AIOW_Operator extends \AIOW_TerminalExpression {

	protected $precedence = 0;
	protected $leftAssoc = true;

	public function getPrecedence() {
		return $this->precedence;
	}

	public function isLeftAssoc() {
		return $this->leftAssoc;
	}

	public function isOperator() {
		return true;
	}

}

class AIOW_Addition extends \AIOW_Operator {

	protected $precedence = 4;

	public function operate(AIOW_Stack $stack) {
		return $stack->pop()->operate($stack) + $stack->pop()->operate($stack);
	}

}

class AIOW_Subtraction extends \AIOW_Operator {

	protected $precedence = 4;

	public function operate(AIOW_Stack $stack) {
		$left = $stack->pop()->operate($stack);
		$right = $stack->pop()->operate($stack);
		return $right - $left;
	}

}

class AIOW_Multiplication extends \AIOW_Operator {

	protected $precedence = 5;

	public function operate(AIOW_Stack $stack) {
		return $stack->pop()->operate($stack) * $stack->pop()->operate($stack);
	}

}

class AIOW_Division extends \AIOW_Operator {

	protected $precedence = 5;

	public function operate(AIOW_Stack $stack) {
		$left = $stack->pop()->operate($stack);
		$right = $stack->pop()->operate($stack);
		return $right / $left;
	}

}

class AIOW_Power extends \AIOW_Operator {

	protected $precedence = 5;

	public function operate(AIOW_Stack $stack) {
		$left = $stack->pop()->operate($stack);
		$right = $stack->pop()->operate($stack);
		return pow($left,$right);
	}
}
