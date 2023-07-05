require( 'jquery-ui-dist/jquery-ui.js' );
require( 'jquery-ui-dist/jquery-ui.css' );
require( 'jquery-ui-dist/jquery-ui.theme.css' );
require( 'blueimp-file-upload/js/jquery.fileupload.js' );

import { humanFileSize } from './humanFileSize.js';

// WORKAROUND: Prevent Double Submiting
global.btnSaveUploadFileClicked = window.btnSaveUploadFileClicked = false;

global.OneUpUploadedResources = window.OneUpUploadedResources = {};
const resourceUploaded = new CustomEvent( "resourceUploaded", {
    detail: {
        name: "resource_uploaded",
    },
});

/**
 * options
 * {
 *     fileuploadSelector: "#OneUpFileUpload",
 *     fileinputSelector: "#upload_file_form_file",
 *     btnStartUploadSelector: "#btnSaveUploadFile",
 *     isStartedHolder: "btnSaveUploadFileClicked",
 *
 *     progressbarSelector: "#FileUploadProgressbar",
 *
 *     fileInputFieldName: "file",
 *     fileResourceKey: "",
 *     fileResourceClass: ""
 * }
 */
export function InitOneUpFileUpload( options )
{
    validateOptions( options );
    
    ///////////////////////////////////////////////////////////////////////
    // https://github.com/blueimp/jQuery-File-Upload/wiki/Options
    ///////////////////////////////////////////////////////////////////////
    $( options.fileuploadSelector ).fileupload({
        url: '' + $( options.fileuploadSelector  ).attr( 'data-endpoint' ),
        type: 'POST',
        dropZone: null,
        fileInput: $( options.fileinputSelector  ),
        maxChunkSize: 1000000,
        autoUpload: false,
        add: function ( e, data )
        {
            $( options.btnStartUploadSelector ).on( 'click', function ( e )
            {
                e.preventDefault();
                //e.stopPropagation();
                
                /*
                if ( window[options.btnStartUploadSelector] ) {
                    return;
                }
                window[options.btnStartUploadSelector]   = true;
                */
                
                $( this ).hide();
                data.submit();
            });
        },
        formData: function ( form )
        {
            //console.log( form );
            let formName    = form[0] ? form[0].name : '';
            
            /*
             * Send Values Needed For PostPersistListener In Backend
             *
             * If Files is Not Wrapped by Form Name Remove It From Here
             */
            return [
                {
                    name: 'formName',
                    value: formName
                },
                {
                    name: 'fileInputFieldName',
                    value: options.fileInputFieldName
                },
                {
                    name: 'fileResourceId',
                    value: getFormFieldValue( form, 'id' )
                },
                {
                    name: 'fileResourceClass',
                    value: getFormFieldValue( form, options.fileResourceClass )
                },
                {
                    name: 'fileResourceKey',
                    value: getFormFieldValue( form, options.fileResourceKey )
                }
            ];
        }
    });
    
    /**
     * FileUpload Event Listeners
     * ===============
     * https://github.com/blueimp/jQuery-File-Upload/wiki/Options#callback-options
     */
    $( options.progressbarSelector ).progressbar({
        value: 0
    });
    
    $( options.fileuploadSelector ).on( 'fileuploadstart', function ( e, data )
    {
        $( options.progressbarSelector ).show();
    });
    
    $( options.fileuploadSelector ).on( 'fileuploadprogress', function ( e, data )
    {
        //console.log( data.loaded, data.total, data.bitrate );
        $( options.progressbarSelector ).progressbar({
            value: data.loaded,
            max: data.total
        });
        
        var progressPercents    = Math.round( ( data.loaded / data.total ) * 100 );
        var progressCaption     = humanFileSize( data.loaded, true ) + ' / ' + humanFileSize( data.total, true ) + ' ( ' + progressPercents + '% )';
        
        $( options.progressbarSelector ).find( 'div.progressInfo > span.caption' ).html( progressCaption );
    });
    
    // Uncomment Console Logs For Debugging
    $( options.fileuploadSelector ).on( 'fileuploaddone', function ( e, data )
    {
        e.preventDefault();
        e.stopPropagation();
        $( options.progressbarSelector ).hide();
        
        //console.log( 'FileUploadDone: ' );
        //console.log( data );
        //console.log( data.result );
        window.OneUpUploadedResources   = {...data.result.resources};
        window.dispatchEvent( resourceUploaded );
    });
}

function validateOptions( options )
{
    let requiredKeys = [
        'fileuploadSelector',
        'fileinputSelector',
        'btnStartUploadSelector',
        'isStartedHolder',
        'progressbarSelector',
        'fileInputFieldName',
        'fileResourceKey',
        'fileResourceClass'
    ];
    let checkAllKeys = requiredKeys.every( ( i ) => options.hasOwnProperty( i ) );
    
    if( ! checkAllKeys ) {
        throw new Error( 'Exception message' );
    }
}

function getFormFieldValue( form, field )
{
    if ( ! form[0] ) {
        return '';    
    }
    
    var formData = form.serializeArray();
    //console.log( formData );
    
    var myFieldName = form[0].name + '[' + field + ']';
    var myFieldFilter = function (field) {
        return field.name == myFieldName;
    }
    
    return formData.filter( myFieldFilter )[0].value;
}
