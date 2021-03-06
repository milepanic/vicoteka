$(document).ready(function() {

	// AJAX CSRF Token
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	// changing tabs on wellcome page
	$(".nav-tabs li a").click(function(e) {
		e.preventDefault();
		var type = $(this).data('type');

		window.location.pathname = '/' + type;
		
		$.ajax({
			type: 'GET',
			url: '/' + type,
			data: type,
			success: function(data) {
				$(this).parent().addClass('active');
				$("#jokes").html(data);
			},
			error: function() {
				/* Act on the event */
			}
		});
	});

	// voting for posts
	$('.vote').click(function() {
		var vote = {
			type: $(this).data('type'),
			id: $(this).data('id')
		};

		// user - bool logged in, defined in jokes.blade.php 
		if(!user) return;

		if($(this).data('type') === 'upvote') {
			if($('#downvote').hasClass('btn-danger'))
				$('#downvote').removeClass('btn-danger');
			$(this).toggleClass('btn-primary');
		}
		if($(this).data('type') === 'downvote') {
			if($('#upvote').hasClass('btn-primary'))
				$('#upvote').removeClass('btn-primary');
			$(this).toggleClass('btn-danger');
		}

		$.ajax({
			type: 'POST',
			url: '/post/vote',
			data: vote,
			success: function (data) {

			},
			error: function() {

			}
		});
	});

	// favoriting posts
	$('.favorite').click(function() {
		var vote = {
			id: $(this).data('id')
		};

		if(!user) return;

		$(this).toggleClass('voted');

		$.ajax({
			type: 'POST',
			url: '/post/favorite',
			data: vote,
			success: function (data) {
				
			},
			error: function() {

			}
		});
	});

	// showing comments
	$('.comments-icon').on('click', function(e) {
		e.preventDefault();

		var comments_container = $(this).parents().eq(2).find('.comments-container');
		comments_container.toggleClass('hidden');

		if(comments_container.hasClass('hidden'))
			return;

		var post_id = $(this).parent().data('id');

		$.ajax({
			method: 'GET',
			url: 'comments/get/' + post_id,
			success: function(data) {
				var comments_box = comments_container.find('.comments-box');
				comments_box.html(data.html);
			},
			error: function() {
				/* Act on the event */
			}
		});
	});

	// editing comments
	$('.edit').on('click', function(e) {
		e.preventDefault();
		var comment_p = $(this).parent().find('.comment');
		var id = comment_p.data('id');
		var comment = comment_p.text();
		comment_p.hide();

		var comment_box = $(this).parent().find('.comment-box');
		$(comment_box).append(
			'<div class="edit-form">' +
			'<input class="comment-edit" type="text" value="' + comment + '" placeholder="Edit comment">' +
			'<button class="comment-edit-button" type="submit">Edit</button>' +
			'</div>'
		);

		$('.comment-edit-button').on('click', function() {
			var edit = { 
				comment: $('.comment-edit').val()
			};

			$.ajax({
				type: 'PATCH',
				url: '/comment/edit/' + id,
				data: edit,
				success: function(data) {
					$('.edit-form').remove();
					comment_p.text(data);
					comment_p.show();
				},
				error: function() {

				}
			});
		});
	});

	// deleting comments
	$('.delete').on('click', function(e) {
		e.preventDefault();
		var comment_div = $(this).parent();
		var id = comment_div.find('.comment').data('id');

		$.ajax({
			url: '/comment/delete/' + id,
			type: 'DELETE',
			success: function() {
				comment_div.remove();
			}
		});
	});

	// reporting posts
	$('.report-post').click(function(e) {
		e.preventDefault();

		var reason = prompt("Unesite razlog prijave\n", "Razlog");
		var id = $(this).parent().data('id');
		$.ajax({
			url: '/report/' + id,
			type: 'POST',
			data: {'reason': reason},
			success: function() {
				alert('Post je uspesno prijavljen');
			},
			error: function() {
				alert('Dogodila se greska, pokusajte ponovo');
			}
		});
	});

});