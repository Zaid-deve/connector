<div class="container-fluid py-4 h-100 w-100">
    <p class="text-muted">Blocked Requests: </p>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Profile</th>
                <th>Actions</th>
            </tr>
        </thead>
        <?php

        // Fetch blocked users
        $stmt = $conn->prepare("
            SELECT DISTINCT u.user_name, u.user_cname, u.user_profile 
            FROM users u
            JOIN user_friends uf 
            ON u.user_id = uf.sender_user_id OR u.user_id = uf.recipient_user_id
            WHERE 
                (uf.sender_user_id = :uid AND uf.is_blocked IS NOT NULL AND uf.is_blocked = :uid)
                OR
                (uf.recipient_user_id = :uid AND uf.is_blocked IS NOT NULL AND uf.is_blocked = :uid)
                AND u.user_id != :uid
        ");
        $stmt->execute([":uid" => $uid]);

        if ($stmt && $stmt->rowCount() > 0) {
            echo "<tbody class='block-list'>";
            $i = 1;
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $peer = $data['user_name'];
                $husername = base64_decode($peer);
                $hname = base64_decode($data['user_cname']);
                $hprofile = User::getProfileUri($data['user_profile']);
                echo "<tr class='block_te'>
                        <th>$i</th>
                        <td><img src='$hprofile' class='img-cover rounded-circle mb-2' height='40px' width='40px'>$husername</td>
                        <td><button class='btn text-primary border-0' onclick='unblockUser(`$peer`, this)'>Unblock</button></td>
                      </tr>";
                $i++;
            }
            echo "</tbody>";
        } else {
            echo "<tbody class='block-list'><tr><td colspan='3' class='text-center text-muted'>No blocked users found.</td></tr></tbody>";
        }

        ?>
    </table>
</div>
