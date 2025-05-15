<?php

return [

    'does_not_exist' => 'Access account does not exist.',
    'assoc_users'    => 'This access account is currently assigned to at least one user and cannot be deleted. Please check in the access first, and then try deleting again. ',

    'create' => [
        'error'   => 'Access account was not created, please try again.',
        'success' => 'Access account created successfully.',
    ],

    'update' => [
        'error'   => 'Access account was not updated, please try again',
        'success' => 'Access account updated successfully.',
    ],

    'delete' => [
        'confirm'   => 'Are you sure you wish to delete this access account?',
        'error'   => 'There was an issue deleting the access account. Please try again.',
        'success' => 'The access account was deleted successfully.',
    ],

    'checkout' => [
        'error'   => 'Access account was not checked out, please try again',
        'success' => 'Access account checked out successfully.',
        'already_checked_out' => 'This access account is already checked out.',
        'user_or_asset_required' => 'You must select a user or asset to assign this access to.',
    ],

    'checkin' => [
        'error'   => 'Access account was not checked in, please try again',
        'success' => 'Access account checked in successfully.',
        'not_checked_out' => 'This access account is not checked out.',
    ],

    'restore' => [
        'error'   => 'Access account was not restored, please try again',
        'success' => 'Access account restored successfully.',
    ],

    'bulk' => [
        'update' => [
            'error'   => 'No access accounts were updated, please try again.',
            'success' => ':success_count access account(s) updated successfully.',
        ],
        'delete' => [
            'error'   => 'No access accounts were deleted, please try again.',
            'success' => ':success_count access account(s) deleted successfully.',
        ],
        'checkout' => [
            'error'   => 'No access accounts were checked out, please try again.',
            'success' => ':success_count access account(s) checked out successfully.',
        ],
        'checkin' => [
            'error'   => 'No access accounts were checked in, please try again.',
            'success' => ':success_count access account(s) checked in successfully.',
        ],
    ],

]; 