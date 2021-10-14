<?php
/**
 * Created by PhpStorm.
 * User: micah
 * Date: 9/10/2019
 * Time: 6:00 AM
 */
?>
<form id="MessageCardContact" action="/cards/card-data/send-text-message-to-card-contact" method="post">
    <input type="hidden" name="card_id" value="<?php echo $intCardId; ?>" />
    <input type="hidden" name="contact_id" value="<?php echo $strContactId ?? ""; ?>" />
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