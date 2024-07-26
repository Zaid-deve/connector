let deleteAccount;
$(function(){

    deleteAccount = function(btn){
        btn = $(btn);
        btn.disabled = true
        btn.text('deleting your account');
        $.post(`${ORIGIN}/php/user/deleteAccount.php`,function(resp){
            const r = JSON.parse(resp)
            if(r.Success){
                location.replace('signin.php');
            } else {
                throwErr(r.Err || 'failed to delete account');
            }
        })
    }

})