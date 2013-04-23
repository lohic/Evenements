/**
 * cordova is available under *either* the terms of the modified BSD license *or* the
 * MIT License (2008). See http://opensource.org/licenses/alphabetical for full text.
 *
 * Copyright (c) Matt Kane 2010
 * Copyright (c) 2011, IBM Corporation
 */


PhoneGap.define("cordova/plugin/BarcodeScanner",
    
    function (require, exports, module) {

        var exec = require("cordova/exec");

        /*var BarcodeScanner = function () {
        };

        //-------------------------------------------------------------------
        BarcodeScanner.Encode = {
            TEXT_TYPE: "TEXT_TYPE",
            EMAIL_TYPE: "EMAIL_TYPE",
            PHONE_TYPE: "PHONE_TYPE",
            SMS_TYPE: "SMS_TYPE",
            //  CONTACT_TYPE: "CONTACT_TYPE",  // TODO:  not implemented, requires passing a Bundle class from Javascript to Java
            //  LOCATION_TYPE: "LOCATION_TYPE" // TODO:  not implemented, requires passing a Bundle class from Javascript to Java
        };

        //-------------------------------------------------------------------
        BarcodeScanner.prototype.scan = function (successCallback, errorCallback) {
            if (errorCallback == null) {
                errorCallback = function () {
                }
            }

            if (typeof errorCallback != "function") {
                console.log("BarcodeScanner.scan failure: failure parameter not a function");
                return
            }

            if (typeof successCallback != "function") {
                console.log("BarcodeScanner.scan failure: success callback parameter must be a function");
                return
            }

            exec(successCallback, errorCallback, 'BarcodeScanner', 'scan', []);
        };

        //-------------------------------------------------------------------
        BarcodeScanner.prototype.encode = function (type, data, successCallback, errorCallback, options) {
            if (errorCallback == null) {
                errorCallback = function () {
                }
            }

            if (typeof errorCallback != "function") {
                console.log("BarcodeScanner.scan failure: failure parameter not a function");
                return
            }

            if (typeof successCallback != "function") {
                console.log("BarcodeScanner.scan failure: success callback parameter must be a function");
                return
            }

            exec(successCallback, errorCallback, 'BarcodeScanner', 'encode', [
                {"type": type, "data": data, "options": options}
            ]);
        };

        var barcodeScanner = new BarcodeScanner();
        module.exports = barcodeScanner;*/
                
                
                var BarcodeScanner = function(){};
                
                BarcodeScanner.prototype.isBarcodeScannerAvailable = function(response){
                cordova.exec(response, null, "BarcodeScannerPlugin", "isBarcodeScannerAvailable", []);
                };
                
                BarcodeScanner.prototype.isBarcodeScannerSetup = function(response){
                cordova.exec(response, null, "BarcodeScannerPlugin", "isBarcodeScannerSetup", []);
                };
                
                //-------------------------------------------------------------------
                BarcodeScanner.Encode = {
                TEXT_TYPE:     "TEXT_TYPE",
                EMAIL_TYPE:    "EMAIL_TYPE",
                PHONE_TYPE:    "PHONE_TYPE",
                SMS_TYPE:      "SMS_TYPE",
                CONTACT_TYPE:  "CONTACT_TYPE",
                LOCATION_TYPE: "LOCATION_TYPE"
                }
                
                //-------------------------------------------------------------------
                BarcodeScanner.prototype.scan = function(success, fail, options) {
                function successWrapper(result) {
                result.cancelled = (result.cancelled == 1)
                success.call(null, result)
                }
                
                if (!fail) { fail = function() {}}
                
                if (typeof fail != "function")  {
                console.log("BarcodeScanner.scan failure: failure parameter not a function")
                return
                }
                
                if (typeof success != "function") {
                fail("success callback parameter must be a function")
                return
                }
                
                if ( null == options )
                options = []
                
                return PhoneGap.exec(successWrapper, fail, "com.cordova.barcodeScanner", "scan", options)
                }
                
                //-------------------------------------------------------------------
                BarcodeScanner.prototype.encode = function(type, data, success, fail, options) {
                if (!fail) { fail = function() {}}
                
                if (typeof fail != "function")  {
                console.log("BarcodeScanner.scan failure: failure parameter not a function")
                return
                }
                
                if (typeof success != "function") {
                fail("success callback parameter must be a function")
                return
                }
                
                return PhoneGap.exec(success, fail, "com.cordova.barcodeScanner", "encode", [{type: type, data: data, options: options}])
                }
                
                cordova.addConstructor(function() {
                                       
                                       /* shim to work in 1.5 and 1.6  */
                                       if (!window.Cordova) {
                                       window.Cordova = cordova;
                                       };
                                       
                                       
                                       if(!window.plugins) window.plugins = {};
                                       window.plugins.barcodeScanner = new BarcodeScanner();
                                       });
                
    });
