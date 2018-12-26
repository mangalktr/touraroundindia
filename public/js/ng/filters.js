/**
 * Created by Ranvir Singh on 27 May 2017.
 * Updated on 26 Jun 2017
 */

// Define our filter
app.filter('filterResultset', function($filter) {
    
  return function(resultset1 ) {

    var i, len; // define vars

    // get resultset1 that have been checked
    var checkedResultset = $filter('filter')(resultset1, {checkedDuration: true});
    var checkedResultset1 = $filter('filter')(resultset1, {checkedDestinations: true});
    var checkedResultsetPrice = $filter('filter')(resultset1, {checkedPriceRange: true});

    // Add in a check to see if any resultset1 were selected. If none, return them all without filters
    if((checkedResultset.length == 0) && (checkedResultset1.length == 0) && (checkedResultsetPrice.length == 0)) {
      return resultset1;
    }

    // get all the unique Durations that come from these checked resultset1
    if( checkedResultset.length != 0 ) {
        var Durations = {};
        for(i = 0, len = checkedResultset.length; i < len; ++i) {
          // if this checked resultset1 Durations isn't already in the Durations object || add it
          if(!Durations.hasOwnProperty(checkedResultset[i].Duration)) {
            Durations[checkedResultset[i].Duration] = true;
          }
        }
    }
    
    // get all the unique Destinationss that come from these checked resultset1
    if( checkedResultset1.length != 0 ) {
        var Destinationss = {};
        for(i = 0, len = checkedResultset1.length; i < len; ++i) {
          if(!Destinationss.hasOwnProperty(checkedResultset1[i].Destinations)) {
            Destinationss[checkedResultset1[i].Destinations] = true;
          }
        }
    }
    
    // get all the unique PriceRanges that come from these checked resultset1
    if( checkedResultsetPrice.length != 0 ) {
        var PriceRanges = {};
        for(i = 0, len = checkedResultsetPrice.length; i < len; ++i) {
          // if this checked resultset1 PriceRanges isn't already in the PriceRanges object 
          // add it
          if(!PriceRanges.hasOwnProperty(checkedResultsetPrice[i].PriceRange)) {
            PriceRanges[checkedResultsetPrice[i].PriceRange] = true;
          }
        }
    }
    
    
    // Now that we have the Durations that come from the checked resultset1, we can get all resultset1 from those Durations and return them
    var ret = [];
    var ret1 = [];
    var returnval = [];
    var returnvalFinal = [];
    var temp    = [];
    
    temp = returnval = resultset1; // result set of json

    if(checkedResultset.length != 0) {
      for(i = 0, len = resultset1.length; i < len; ++i) {
        if((Durations[resultset1[i].Duration]) ) {
          ret.push(resultset1[i]);
        } 
      }
        temp = ret; // overwrite the filtered array
    }
    
   
    if(checkedResultset1.length != 0) {
        returnval = [];
      for(i = 0, len = temp.length; i < len; ++i) {
      // If this resultset's Duration exists in the Durations object, add it to the return array
        if((Destinationss[temp[i].Destinations])) {
          returnval.push(temp[i]);
        }
      }
        temp = returnval; // overwrite the filtered array
    }
    

    if(checkedResultsetPrice.length != 0) {
        ret1 = [];
      for(i = 0, len = temp.length; i < len; ++i) {
      // If this resultset's PriceRange exists in the PriceRanges object, add it to the return array
        if((PriceRanges[temp[i].PriceRange])) {
          ret1.push(temp[i]);
        }
      }
      temp = ret1; // overwrite the filtered array
    }

    // we have our result!
    returnvalFinal = temp;
    return returnvalFinal;
  };

});

/*

// Define our filter
app.filter('filterResultsetCustom', function($filter) {
    
  return function( resultset1 , types ) {

    var i, len; // define vars

    // get resultset1 that have been checked
    var checkedResultset = $filter('filter')(resultset1, {searchCategory: true});

//console.log(checkedResultset);
//console.log( resultset1 );

//console.log(searchDestinations);

    // Add in a check to see if any resultset1 were selected. If none, return them all without filters
    if((checkedResultset.length == 0)) {
      return resultset1;
    }

    // get all the unique Categories that come from these checked resultset1
    if( checkedResultset.length != 0 ) {
        var Categories = {};
        for(i = 0, len = checkedResultset.length; i < len; ++i) {
          // if this checked resultset1 Categories isn't already in the Categories object || add it

//console.log(checkedResultset[i].PackageTypeArr);
          if(!Categories.hasOwnProperty(checkedResultset[i].Duration)) {
            Categories[checkedResultset[i].Duration] = true;
          }
        }
    }
    
    
    // Now that we have the Categories that come from the checked resultset1, we can get all resultset1 from those Categories and return them
    var ret = [];
    var ret1 = [];
    var returnval = [];
    var returnvalFinal = [];
    var temp    = [];
    
    temp = returnval = resultset1; // result set of json

    if(checkedResultset.length != 0) {
      for(i = 0, len = resultset1.length; i < len; ++i) {
        if((Categories[resultset1[i].Duration]) ) {
          ret.push(resultset1[i]);
        } 
      }
        temp = ret; // overwrite the filtered array
    }
     

    // we have our result!
    returnvalFinal = temp;
    return returnvalFinal;
  };

});

*/


 // filter to use pick unique values 
app.filter('rvUnique', function() {
   return function(collection, keyname) {
      var output = [], 
          keys = [];

      angular.forEach(collection, function(item) {
          var key = item[keyname];
          if(keys.indexOf(key) === -1) {
              keys.push(key);
              output.push(item);
          }
      });

      return output;
   };
});
