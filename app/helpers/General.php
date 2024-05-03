<?php

namespace Helpers;

use Core\Auth;
use Core\View;

class General {
    public static function addOnlinePing(): void
    {
        View::push('
        <script>
    document.addEventListener("DOMContentLoaded", function() {
        updateLastTimeOnline()

        function updateLastTimeOnline() {
            $.ajax({
                type: "POST",
                url: "/user/updateonlinestatus",
                data: "userId='. Auth::user()->rID(). '",
                success: function (response) {
                }
            });
        }
    });

</script>', 'custom')->toFooter();
    }
}