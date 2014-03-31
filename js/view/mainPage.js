/******************
** PHP VARIABLES **
*******************/
var base_url = $('#base_url').val(); // Base url of server/application

/*********************
** GLOBAL VARIABLES **
**********************/
var challenged = false;

/***********************
** mainPage FUNCTIONS **
************************/
/*
 * Show invitation panel and challenger
 */
function showInvitation(challenger) {
	$('.screen-blocker').show();
	$('.invitation').show();
	$('#challenger').html(challenger);
}

/*
 * Hides invitation panel
 */
function hideInvitation() {
	$('.screen-blocker').hide();
	$('.invitation').hide();
}

/*
 * Shows waiting panel
 */
function showWaiting() {
	$('.screen-blocker').show();
	$('.waiting').show();
}

/*
 * Hides waiting panel
 */
function hideWaiting() {
	$('.screen-blocker').hide();
	$('.waiting').hide();
}

$(function () {
	// Hide GUI elements
	$('.screen-blocker').hide();
	$('.invitation').hide();
	$('.waiting').hide();
	
	// Sets Bootstrap dropdown handler to dropdown
	$('.dropdown-toggle').dropdown();
	
	// Update online users section
	$('#onlineUsers').everyTime(2000, function() {
		$('#onlineUsers').load(base_url + 'arcade/getOnlineUsers', function() {
			// Assign click event handlers on challenge buttons
			$('.btn.btn-success').on('click', function() {
				var url = base_url + 'arcade/invite?login=' + $(this).attr('id');
				$.post(url, function (data,textStatus,jqXHR) {
					data = JSON.parse(data);
					if (data.status == 'success') {
						challenged = true;
						showWaiting();
					}
				});
			})
		});
		
		// Updates number of online users
		$('#num_users_label').html($('#num_users').val());
		
		$.getJSON(base_url + 'arcade/getInvitation', function(data, text, jqZHR) {
			if (data && data.invited) {
				var user = data.login;
				showInvitation(user);
			} else {
				if (!challenged) // Hide only if cancel request was for invitation
					hideInvitation();
			}
		});
	});
	
	// Update leaderboards from server
	$('#leaderboard').everyTime(2000, function() {
		$('#leaderboard').load(base_url + 'arcade/getLeaderboard');
	});
	
	$('#invitation-accept').on('click', function () {
		$.getJSON(base_url + 'arcade/getInvitation',function(data, text, jqZHR) {
			if (data && data.invited) {
				$.getJSON(base_url + 'arcade/acceptInvitation', function(data, text, jqZHR) {
					if (data && data.status == 'success') {
						$('.invitation').hide();
						window.location.href = base_url + 'board/index';
					}
				});
			}
		});
	});
	
	$('#invitation-decline').on('click', function () {
		$.getJSON(base_url + 'arcade/getInvitation',function(data, text, jqZHR) {
			if (data && data.invited) {
				hideInvitation();
				$.post(base_url + 'arcade/declineInvitation');
			}
		});
	});
	
	$('#waiting-cancel').on('click', function() {
		$.post(base_url + 'arcade/declineInvitation');
		hideWaiting();
		challenged = false;
	});
	
	$('body').everyTime(2000, function() { // Checks invitation for waiting player
		if (challenged) {
			var url = base_url + 'arcade/checkInvitation';
			$.getJSON(url, function (data, text, jqZHR) {
				if (data && data.status=='rejected') {
					alert('Sorry, your invitation to play was declined!');
					hideWaiting();
					challenged = false;
				}
				if (data && data.status=='accepted') {
					window.location.href = base_url + 'board/index';
				}
			});
		}
	});
});