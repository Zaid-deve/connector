<div class="container-fluid py-4 d-flex h-100 w-100">
    <div class="m-auto form-container">
        <form action="#" id="profileEditForm">
            <h3 class="m-0">Edit Profile</h3>
            <small>changes are visible to other users</small>
            <div class="d-flex flex-column gap-2 mt-3">
                <div class="d-flex flex-column align-items-center">
                    <div class="position-relative m-auto">
                        <label for="__user__profile__inp">
                            <img src="<?php echo $profile ?>" alt="user profile" class="rounded-circle img-cover __preview__imgsrc">
                            <small class="text-danger fw-light text-center mt-2 __user__profile__err"></small>
                            <div class="profile-change-icon position-absolute"><i class="ri-pencil-line"></i></div>
                            <input type="file" id="__user__profile__inp" name="profile_img" accept="image/*" hidden>
                        </label>
                    </div>
                    <div class="err text-danger profile-err text-center"></div>
                </div>
                <div class="field field-username">
                    <label for="__username">user name</label>
                    <input type="text" class="form-control" id="__username" name="username" value="<?php echo $username ?>">
                    <div class="err text-danger"></div>
                </div>
                <div class="field field-name">
                    <label for="__name">name</label>
                    <input type="text" class="form-control" id="__name" name="name" value="<?php echo $name; ?>">
                    <div class="err text-danger"></div>
                </div>
                <div class="field field-email">
                    <label>Email Address</label>
                    <input type="text" class="form-control" value="<?php echo $email ?>" readonly>
                </div>
                <button class="btn btn-primary w-100 py-2 mt-3 rounded-5 d-none" type="submit" id="modifyProfileBtn">
                    <span class="text-light fw-bold btn-text">Continue</span>
                    <i class="ri-arrow-right-line ms-1 text-light"></i>
                </button>
            </div>
        </form>
    </div>
</div>