<?php

namespace App\Website\Vue\Classes\Base;

class VueCustomMethods
{
    public static function renderSortMethods()
    {
        return '
            orderBy: function(list,key,type) {
                let newList = {};
                switch(type) {
                    case "desc":
                        newList = Object.keys(list).sort((a, b) => a[key] < b[key] ? 1 : -1);
                    default:
                        newList = Object.keys(list).sort((a, b) => a[key] > b[key] ? 1 : -1);
                }

               return newList.reduce((res, key) => (res[key] = list[key], res), {});
            },
            sortedEntity: function (searchQuery, entity, orderkey, sortByType, pageIndex, pageDisplay, pageTotal, callback, filterFields, filterList)
            {
                var returnData = {};
                returnData.pageIndex = pageIndex;
                
                let objOrderedEntity = _.orderBy(entity, orderkey, sortByType ? "asc" : "desc");
                
                let intStartIndex = ((returnData.pageIndex-1) * pageDisplay);
                let intIndexOffset = entity.length - intStartIndex;
                let intEndIndex = intStartIndex + (( pageDisplay <= intIndexOffset ) ? pageDisplay : intIndexOffset);

                if (!searchQuery) {
                    var intTotalPages = 1;

                    if (pageDisplay < objOrderedEntity.length) {
                        intTotalPages = objOrderedEntity.length / pageDisplay;
                    }

                    returnData.pageTotal = Math.ceil(intTotalPages);

                    if ( typeof callback === "function") {
                        callback(returnData);
                    }
                    
                    return objOrderedEntity.slice(intStartIndex, intEndIndex);;
                }
                
                let intShouldSkip = [];
                let objFilterColumn = "";

                if ( typeof filterList !== "undefined")
                {
                    objFilterColumn = $("#" + filterList).val();
                }

                let objFilteredEntity = objOrderedEntity.filter(function (currEntity)
                {
                    let searchRegex = new RegExp(searchQuery, "i");
                    let intFoundMatch = false;
                    let arFoundMatch = false;
                    let arEntityKeys = Object.keys(currEntity);

                    for (let entityField in currEntity)
                    {
                        if ( objFilterColumn !== "")
                        {
                            if (objFilterColumn !== "everything" && entityField !== objFilterColumn)
                            {
                                continue;
                            }
                        }

                        if ( typeof filterFields !== "undefined")
                        {
                            if(arFoundMatch[entityField] === true)
                            {
                                continue;
                            }

                            for (let indexFilters in filterFields)
                            {
                                if (arEntityKeys[entityField] == filterFields[indexFilters] )
                                {
                                    arFoundMatch[entityField] = true;
                                    continue;
                                }
                            }
                        }

                        if (searchRegex.test(currEntity[entityField])) {

                            intFoundMatch = true;
                        }
                    }

                    if (intFoundMatch == true) {

                        if ( typeof callback === "function") {
                            callback(returnData);
                        }

                        return currEntity;
                    }
                });

                let intOrderedIndexOffset = objOrderedEntity.length - intStartIndex;
                let intOrderedEndIndex = intStartIndex + (( pageDisplay <= intOrderedIndexOffset ) ? pageDisplay : intOrderedIndexOffset);

                if (objFilteredEntity.length < intStartIndex) {

                    intStartIndex = Math.floor(objFilteredEntity.length / pageDisplay) * pageDisplay;
                    returnData.pageIndex =  Math.ceil(objFilteredEntity.length / pageDisplay);
                    intOrderedIndexOffset = objFilteredEntity.length - intStartIndex;
                    intOrderedEndIndex = intStartIndex + intOrderedIndexOffset;
                }

                var intTotalFilteredPages = 1;

                if (pageDisplay < objFilteredEntity.length) {
                    intTotalFilteredPages = objFilteredEntity.length / pageDisplay;
                }

                returnData.pageTotal = Math.ceil(intTotalFilteredPages);

                if ( typeof callback === "function") {
                    callback(returnData);
                }

                return objFilteredEntity.slice(intStartIndex, intOrderedEndIndex);
            },';
    }
}