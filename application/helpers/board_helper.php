<?php
/*
 * Constants for the board dimensions
 */
define('BOARD_WIDTH', 7);
define('BOARD_HEIGHT', 6);

/*
 * Returns the number of turns that have been made on the board
 */
function getNumTurns($board) {
	$turns = 0;
	for ($i = 0; $i < count($board); $i++) {
		if ($board[$i] != 0)
			$turns++;
	}
	return $turns;
}

/*
 * Returns the index of the user who last made a move on the board
 */
function getLastUserIndex($board) {
	return getNumTurns($board) % 2 == 1 ? 1 : 2;
}

/*
 * Returns the row of a given position
 */
function getRow($position) {
	return floor($position / BOARD_WIDTH);
}

/*
 * Returns the column of a given position
 */
function getColumn($position) {
	return $position % BOARD_WIDTH;
}

/*
 * Returns the minimum distance of a direction to the edge of a board 
 * from a given position
 */
function getMin($position, $direction) {
	$row = getRow($position);
	$column = getColumn($position);
	$invertedRow = BOARD_HEIGHT - 1 - $row;
	$invertedColumn = BOARD_WIDTH - 1 - $column;
	$minimum = 3;
	switch($direction) {
		case 'N': $minimum = $invertedRow;
			break;
		case 'NE': $minimum = min($invertedColumn, $invertedRow);
			break;
		case 'E': $minimum = $invertedColumn;
			break;
		case 'SE': $minimum = min($invertedColumn, $row);
			break;
		case 'S': $minimum = $row;
			break;
		case 'SW': $minimum = min($column, $row);
			break;
		case 'W': $minimum = $column;
			break;
		case 'NW': $minimum = min($column, $invertedRow);
			break;
	}
	return min(3, $minimum);
}

/*
 * Returns the match status of a board after a given move.
 */
function getMatchStatus($board, $position) {
	$userIndex = getLastUserIndex($board);
	if (hasFour($board, $position, $userIndex, array('W', 'E'), 1) ||
		hasFour($board, $position, $userIndex, array('S', 'N'), BOARD_WIDTH) ||
		hasFour($board, $position, $userIndex, array('SE', 'NW'), BOARD_WIDTH -1) ||
		hasFour($board, $position, $userIndex, array('SW', 'NE'), BOARD_WIDTH + 1))
		return 1 + $userIndex;
	else if (isTie($board))
		return 0;
	return 1;
}

/*
 * Returns whether a board is completely filled or not
 */
function isTie($board) {
	for ($i = 0; $i < count($board); $i++) {
		if ($board[$i] == 0)	
			return false;
	}
	return true;
}

/*
 * Returns whether there is four in a row in a given position, direction
 * on a board
 */
function hasFour($board, $position, $userIndex, $direction, $offset) {
	for ($i = $position - $offset*getMin($position, $direction[0]); $i <= $position; $i += $offset) {
		$count = 0;
		for ($j = $i; $j <= $i + $offset*getMin($i, $direction[1]); $j += $offset) {
			if ($board[$j] == $userIndex)
				$count++;
		}
		if ($count == 4)
			return true;
	}
	return false;
}

/*
 * Returns whether the board, board to previous board relationship and
 * latest move is possible and/or valid
 */
function getBoardValidity($board, $prevBoard, $position) {
	if (hasPositionOutOfBounds($position))
		return 'Last position inputted is out of bounds.';
	if (hasNotMadeExactlyOneMove($board, $prevBoard))
		return 'New board position has not progressed forward or has not made exactly one move.';
	if (hasIncorrectNumColors($board))
		return 'Incorrect number of colored discs.';
	if (hasFloatingDiscs($board))
		return 'Floating discs.';
	return 'valid';
}

/*
 * Returns true if a position is out of bounds of the board
 * dimensions, otherwise false
 */
function hasPositionOutOfBounds($position) {
	return $position < 0 || $position >= BOARD_WIDTH*BOARD_HEIGHT;
}

/*
 * Returns true if the board has made one new move on the board
 * compared to the previous board position, otherwise false
 */
function hasNotMadeExactlyOneMove($board, $prevBoard) {
	if (!isset($prevBoard))
		return getNumTurns($board) != 1;
	$diff = true;
	for ($i = 0; $i < BOARD_WIDTH*BOARD_HEIGHT; $i++) {
		if ($board[$i] != $prevBoard[$i]) {
			if (!$diff || $prevBoard[$i] != 0) {
				return true;
			} else {
				$diff = false;
			}
		}
	}
	return $diff;
}

/*
 * Returns true if the board has an incorrect number of
 * colored discs, otherwise false
 */
function hasIncorrectNumColors($board) {
	$red = 0;
	$yellow = 0;
	for ($i = 0; $i < BOARD_WIDTH*BOARD_HEIGHT; $i++) {
		if ($board[$i] == 1)
			$red++;
		else if ($board[$i] == 2)
			$yellow++;
	}
	return $red - $yellow > 1 || $red - $yellow < 0;
}

/*
 * Returns true if the board has floating discs on the board,
 * otherwise false
 */
function hasFloatingDiscs($board) {
	$column_floats = array_fill(0, BOARD_WIDTH, false);
	for ($i = BOARD_WIDTH*BOARD_HEIGHT - 1; $i >= 0; $i--) {
		if ($board[$i] != 0)
			$column_floats[getColumn($i)] = true;
		else {
			if ($column_floats[getColumn($i)])
				return true;
		}
	}
	return false;
}
?>