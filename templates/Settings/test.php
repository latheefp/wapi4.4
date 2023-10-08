<form method="post" accept-charset="utf-8" class="form-horizontal needs-validation" id="newuser_form" defaction="/settings/ajxedituser" novalidate="novalidate" action="/settings/edituser/6">
    <div style="display:none;">
        <input type="hidden" name="_csrfToken" autocomplete="off" value="TL0x7Jt9wwKb5kRAwsLpekYDx4S8fShHFFAXPFDhxWDUdjVZ30Li9/6U0/TUBc0GnVGGMxdl3xsdv49sqf0HQ+jFn/wXQ4ETczP3vYVMTHJ3Yi8ENtX2Vu1CDGHuJteUyqPjkLVk9GWqaLcCL1LsKw==">
    </div>             

    <input type="hidden" value="6" name="id" id="id">

    <div class="row" data-select2-id="15">
        <div class="form-group col-xl-6">
            <label for="name">Name *</label>
            <input type="text" class="form-control" name="name" id="name" required="" placeholder="Name" value="Administrator">
        </div>

        <div class="form-group col-xl-3">
            <label for="email">Email Id</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Email Id" value="latheef1p@gmail.com">
        </div>

        <div class="form-group col-xl-3">
            <label for="phone">Mobile No. *</label>
            <input type="text" class="form-control" name="phone" id="phone" required="" placeholder="Mobile No." value="">
        </div>

        <div class="form-group col-xl-3">
            <label class="control-label" for="group"><span data-toggle="tooltip" title="(Autocomplete)">User Type *</span></label>
            <input type="text" name="group" source="8754781096C32282" value="" placeholder="User Type" id="group" class="form-control ac_autocomplete"> 
            <input type="hidden" name="group_id" id="group_id" value="">
        </div>

        <div class="form-group col-xl-3">
            <label for="remarks">Notes</label>
            <textarea placeholder="Remarks" name="remarks" class="form-control" rows="2"></textarea>
        </div>

        <div class="form-group col-xl-3" data-select2-id="37">
            <label class="control-label" for="status">Status</label>
            <select class="form-control" required="" name="status" data-select2-id="10" tabindex="-1" aria-hidden="true">
                <option value="Active" data-select2-id="12">Active</option>
                <option value="Deactive" data-select2-id="38" selected="">Deactive</option>
            </select>
        </div>

        <div class="form-group col-xl-3">
            <label for="username">Login *</label>
            <input type="text" class="form-control" name="username" id="username" required="" autocomplete="off" placeholder="Username" value="admin">
        </div>
    </div






