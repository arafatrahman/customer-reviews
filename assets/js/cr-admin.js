jQuery(document).ready(function($) {

    $('#star_color').on('input', function() {
        $(this).attr('value', $(this).val());
    });

      $('.reply-now').on('click', function() {

      $('#reply-review-id').val($(this).data('review-id'));
      $('#reply-review-author').text($(this).data('review-author'));
      $('#reply-message').val($(this).data('reply-message') || '');
      $('#cr-reply-popup').show();
      });

      $('#close-reply-popup').on('click', function() {
      $('#cr-reply-popup').hide();
      });

      $('#reply-form').on('submit', function(event) {
      event.preventDefault();

      let reviewId = $('#reply-review-id').val();
      let replyMessage = $('#reply-message').val();

      if (!replyMessage.trim()) {
          alert('Reply message cannot be empty.');
          return;
      }

      $.ajax({
          url: cradmin_ajax.ajax_url, 
          method: 'POST',
          data: {
              action: 'save_review_reply',
              review_id: reviewId,
              reply_message: replyMessage
          },
          success: function(response) {
              if (response.success) {
                  alert('Reply submitted successfully.');
                  $('#cr-reply-popup').hide();
              location.reload();
              } else {
                  alert('Failed to submit reply: ' + response.data);
              }
          },
          error: function() {
              alert('An error occurred while submitting the reply.');
          }
      });
      });

      $('#select-all').on('click', function() {
      let isChecked = $(this).prop('checked');
      $('input[name=\"review_ids[]\"]').prop('checked', isChecked);
      });
  });



// Example usage: Add this to an element in your HTML
// <a href="#" onclick="showTab(event, 'general')">General</a>