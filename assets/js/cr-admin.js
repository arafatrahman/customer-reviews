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

    $('.edit-review').on('click', function() {
       
        $('#edit-review-id').val($(this).data('review-id'));
        $('#edit-review-name').val($(this).data('review-author'));
        $('#edit-review-email').val($(this).data('review-email'));
        $('#edit-review-phone').val($(this).data('review-phone'));
        $('#edit-review-website').val($(this).data('review-website'));
        $('#edit-review-comment').val($(this).data('review-comment'));
        $('#edit-review-city').val($(this).data('review-city'));
        $('#edit-review-state').val($(this).data('review-state'));
        $('#edit-review-status').val($(this).data('review-status'));
        $('#edit-review-rating').val($(this).data('review-rating'));

        $('#cr-edit-review-popup').show();
    });

    $('#close-edit-review-popup').on('click', function() {
        $('#cr-edit-review-popup').hide();
    });

    $('#update-customer-review').on('click', function(event) {
        alert('Form submitted!');
        // Prevent the default form submission
        event.preventDefault();
       
        let reviewId = $('#edit-review-id').val();
        let reviewName = $('#edit-review-name').val();
        let reviewEmail = $('#edit-review-email').val();
        let reviewPhone = $('#edit-review-phone').val();
        let reviewWebsite = $('#edit-review-website').val();
        let reviewComment = $('#edit-review-comment').val();
        let reviewCity = $('#edit-review-city').val();
        let reviewState = $('#edit-review-state').val();
        let reviewStatus = $('#edit-review-status').val();
        let reviewRating = $('#edit-review-rating').val();
        let reviewTitle = $('#edit-review-title').val();
        $.ajax({
            url: cradmin_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'edit_customer_review',
                review_id: reviewId,
                review_name: reviewName,
                review_email: reviewEmail,
                review_phone: reviewPhone,
                review_website: reviewWebsite,
                review_comment: reviewComment,
                review_city: reviewCity,
                review_state: reviewState,
                review_status: reviewStatus,
                review_rating: reviewRating,
                review_title: reviewTitle
            },
            success: function(response) {
                if (response.success) {
                    alert('Review updated successfully.');
                    $('#cr-edit-review-popup').hide();
                    location.reload();
                } else {
                    console.log(response.data);
                    alert('Failed to update review: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while updating the review.');
            }
        });
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