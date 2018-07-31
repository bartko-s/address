"use strict";

$(document).ready(function() {
    $('#postcode').autocomplete({
        minLength: 2,
        source: function(request, response) {
            $.ajax( {
                url: '/api/address',
                data: {
                    fields: ['postcode', 'post_office'],
                    filters: {
                        postcode :request.term
                    },
                    orders: ['postcode'],
                    limit: 15
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        select: function( event, ui ) {
            $( "#postcode" ).val( ui.item.postcode );
            return false;
        }
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
            .append( "<div>" + item.postcode + " , pošta: " + item.post_office + "</div>" )
            .appendTo( ul );
    };

    $('#city').autocomplete({
        minLength: 2,
        source: function( request, response ) {
            $.ajax( {
                url: '/api/address',
                data: {
                    fields: ['city', 'postcode', 'post_office'],
                    filters: {
                        city :request.term
                    },
                    orders: ['city'],
                    limit: 15
                },
                success: function( data ) {
                    response( data );
                }
            } );
        },
        select: function( event, ui ) {
            $( "#city" ).val( ui.item.city );
            $( "#postcode" ).val( ui.item.postcode );
            return false;
        }
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
            .append( "<div>" + item.city + ", " + item.postcode + " , pošta: " + item.post_office + "</div>" )
            .appendTo( ul );
    };

    $('#street').autocomplete({
        minLength: 2,
        source: function( request, response ) {
            var queryParams = {
                fields: ['street', 'city', 'postcode', 'post_office'],
                filters: {
                    street: request.term,
                },
                orders: ['street', 'city', 'postcode'],
                limit: 15
            };

            $.ajax( {
                url: '/api/address',
                data: queryParams,
                success: function( data ) {
                    response( data );
                }
            } );
        },
        select: function( event, ui ) {
            $( "#street" ).val( ui.item.street );
            $( "#city" ).val( ui.item.city );
            $( "#postcode" ).val( ui.item.postcode );
            return false;
        }
    })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>" )
            .append( "<div>" + item.street + ", " + item.city + ", " + item.postcode + ", pošta: " + item.post_office +"</div>" )
            .appendTo( ul );
    };
});