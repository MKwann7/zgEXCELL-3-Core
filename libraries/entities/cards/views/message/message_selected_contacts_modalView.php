<?php
/**
 * Created by PhpStorm.
 * User: micah
 * Date: 9/10/2019
 * Time: 6:00 AM
 */
?>
<form id="MessageCardContacts" action="/cards/card-data/send-text-message-to-selected-card-contacts" method="post">
    <input type="hidden" name="card_id" value="<?php echo $intCardId; ?>" />
    <?php foreach($arContacts as $currContact) { ?>
        <input type="hidden" name="contact[]" value="<?php echo $currContact; ?>" />
    <?php } ?>
    <table class="table" style="margin-bottom: 5px; margin-top:10px;">
        <tr>
            <td style="width:100px;vertical-align: middle;">Text Message</td>
            <td>
                <textarea class="form-control" name="text_message_data"></textarea>
            </td>
        </tr>
    </table>
    <button class="btn btn-primary w-100">Send Text Message</button>
</form>