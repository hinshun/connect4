/******************
** PHP VARIABLES **
*******************/
var base_url = $('#base_url').val(); // Base url of server/application

/*********************
** GLOBAL VARIABLES **
**********************/
var chat = $('.chat'); // JQuery selector for the chat
var lastChatter = ''; // The last user who has chatted

/*******************
** CHAT FUNCTIONS **
********************/
/*
 * Initializes the chat and 
 */
function initializeChat() {
	$('.form-control').focus();
	
	// Sends system messages to chat
	addSystemMessage(otherUserFirst + ' has joined the game.');
	addSystemMessage(userFirst + ' has joined the game.');
}

/*
 * Runs debug commands, uncomment to reactivate
 */
function runCommand(msg) {
	/*var tokens = msg.split(' ');
	var errMsg = '';
	if (tokens.length == 1 && tokens[0] == '/debug') {
		var json = JSON.stringify(Game);
		errMsg += json + '!';
		errMsg += Game.getPlayerTurn() + '!';
		errMsg += Game.getUserIndex(user);
		addSystemMessage(errMsg);
	} else if (tokens.length == 2 && tokens[0] == '/load') {
		var debugTokens = tokens[1].split('!');
		if (debugTokens.length == 3) {
			var debugGame = JSON.parse(debugTokens[0]);
			Game.user1_time = debugGame.user1_time;
			Game.user2_time = debugGame.user2_time;
			Game.started = debugGame.started;
			Game.refresh(debugGame.board);
			Game.postBoard();
			addSystemMessage('Player Turn: ' + debugTokens[1]);
			addSystemMessage('User Index: ' + debugTokens[2]);
			Game.checkTurn();
		}
	} else if (tokens.length == 1 && tokens[0] == '/reset') {
		Game.initialize();
		Game.clear();
		Game.board = null;
		Game.postBoard();
		Game.checkTurn();
	}*/
}

/*
 * Adds an user's message to the chat
 */
function addUserMessage(msg) {
	chat.append('<li class="chat-wrapper you"><div class="chat-arrow right"></div><div class="chat-bubble right"><span class="chat-msg">' + msg + '</span></div></li>');
	lastChatter = 'user';
	updateChatScroll();
}

/*
 * Adds an opponent's message to the chat
 */
function addOtherMessage(msg) {
	var html = '';
	if (lastChatter != 'other')
		html = '<span class="chat-user">' + otherUserFirst + '</span>';
	chat.append('<li class="chat-wrapper">' + html + '<div class="chat-arrow left"></div><div class="chat-bubble left"><span class="chat-msg">' + msg + '</span></div></li>');
	lastChatter = 'other';
	updateChatScroll();
}

/* 
 * Adds a system message to the chat
 */
function addSystemMessage(msg) {
	chat.append('<li class="chat-system">' + msg + '</li>');
	updateChatScroll();
}

/*
 * Sets chat scrollbar to the bottom
 */
function updateChatScroll() {
	var chat = $('.chat');
	chat.scrollTop(chat.get(0).scrollHeight);
}

/*
 * Sets event handlers for chat events
 */
function setChatEventHandlers() {
	$('body').on('click', function () { // Always focus on chat input
		$('.form-control').focus();
	});
	
	$('form').submit(function() { // POST msg to server
		var msgSelector = $('[name="msg"]');
		var msg = msgSelector.val();
		if (msg.length > 0) {
			// Debug commands are commented out
			/*if (msg.substring(0, 1) == '/') // If prefixed by /, it is a debug command
				runCommand(msg);
			else {*/
				var arguments = $(this).serialize();
				var url = base_url + 'board/postMsg';
				$.post(url, arguments, function (data,textStatus,jqXHR) {
					addUserMessage(msg);
				});
			//}
		}
		msgSelector.val(''); // Clears chat input
		return false;
	});	
}

$(function () {
	initializeChat();
	setChatEventHandlers();
});