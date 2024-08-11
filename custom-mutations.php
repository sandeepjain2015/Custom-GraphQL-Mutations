<?php
/**
 * Plugin Name: Custom GraphQL Mutations
 * Description: Adds custom GraphQL mutations.
 * Version: 1.0
 * Author:Sandeep jain
 */

add_action( 'graphql_register_types', 'register_update_user_profile_mutation' );

function register_update_user_profile_mutation() {
    register_graphql_mutation( 'updateUserProfile', [
        'inputFields' => [
            'userId' => [
                'type' => 'ID',
                'description' => __( 'The ID of the user', 'your-textdomain' ),
            ],
            'firstName' => [
                'type' => 'String',
                'description' => __( 'The first name of the user', 'your-textdomain' ),
            ],
            'lastName' => [
                'type' => 'String',
                'description' => __( 'The last name of the user', 'your-textdomain' ),
            ],
        ],
        'outputFields' => [
            'user' => [
                'type' => 'User',
                'description' => __( 'The updated user', 'your-textdomain' ),
            ],
        ],
        'mutateAndGetPayload' => function( $input, $context, $info ) {
            $user_id = $input['userId'];
            $first_name = $input['firstName'];
            $last_name = $input['lastName'];

            // Update the user data
            $update = wp_update_user([
                'ID' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
            ]);

            // Error handling: Check if the update was successful
            if ( is_wp_error( $update ) ) {
                throw new Exception( $update->get_error_message() );
            }

            // Retrieve the updated user
            $user = get_user_by( 'ID', $user_id );
            graphql_debug( $user, [ 'type' => 'USER_BREAKPOINT' ] );
            $user = [
                "id"=>$user_id,
                "firstName"=> get_user_meta( $user_id, 'first_name', true ),
                "lastName"=> get_user_meta( $user_id, 'last_name', true )
            ];
            return [
                'user' => $user
            ];
        }
    ]);
}
