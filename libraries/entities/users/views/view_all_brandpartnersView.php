<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:43 AM
 */

$this->CurrentPage->BodyId            = "view-all-brandpartners-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-all-brandpartners-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Brand Partners | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "Brand Partners";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Home</span>
        </a> &#187;
        <a href="/account/admin" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Admin</span>
        </a> &#187;
        <span class="breadCrumbPage">Brand Partners</span>
    </div>
</div>
<div class="BodyContentBox">
    <style type="text/css">
        .BodyContentBox .table-striped td {
            width:10%;
        }
        .BodyContentBox .table-striped td:first-child {
            width:5%;
        }
        .BodyContentBox .table-striped td:nth-child(5) {
            width:5%;
        }
    </style>
    <div id="app" class="formwrapper" >
        <div class="formwrapper-control" v-cloak>
            <div class="fformwrapper-header">
                <table class="table header-table" style="margin-bottom:0px;">
                    <tbody>
                    <tr>
                        <td>
                            <h3 class="account-page-title">Brand Partners</h3>
                            <div class="form-search-box" v-cloak>
                                <input v-model="searchQuery" class="form-control" type="text" placeholder="Search for..."/>
                            </div>
                        </td>
                        <td class="text-right page-count-display" style="vertical-align: middle;">
                            <span class="page-count-display-data">
                                Current: <span>{{ pageIndex }}</span>
                                Pages: <span>{{ totalPages }}</span>
                            </span>
                            <button v-on:click="prevPage()" class="btn prev-btn" :disabled="pageIndex == 1">Prev</button>
                            <button v-on:click="nextPage()" class="btn" :disabled="pageIndex == totalPages">Next</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <table class="table table-striped">
                <thead>
                <th v-for="column in columns">
                    <a v-on:click="orderByColumn(column)" v-bind:class="{ active : orderKey == column, sortasc : sortByType == true, sortdesc : sortByType == false }">
                        {{ column | ucWords }}
                    </a>
                </th>
                <th class="text-right">
                    Actions
                </th>
                </thead>
                <tbody>
                <tr v-for="person in orderedPeople" v-on:dblclick="editColumn(person)">
                    <td>{{ person.user_id }}</td>
                    <td>{{ person.username }}</td>
                    <td>{{ person.first_name }}</td>
                    <td>{{ person.last_name }}</td>
                    <td>{{ person.status }}</td>
                    <td>{{ person.created_on }}</td>
                    <td class="text-right">
                        <img v-on:click="editColumn(person)" class="pointer" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACYAAAAmCAYAAACoPemuAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAABmJLR0QA/wD/AP+gvaeTAAAAB3RJTUUH4gkUCx4h0U5y8AAAA6xJREFUWMPN2EtoXFUcx/FPbiKK1kqbWkurWVjFirHWB1WKSrFudGMUhSr42mjx0Soo+NiIK1EUFYtmo+BCUBEqFio+sNGSSkVahRjtFBu01UVaMal9QJ3Exf9MejOZSWbiTegPhuGeOffO9/7P//wfp0UTunATaMUSXIaVuBiLcAZGcRh/og878GO6Hil1Nf5fLQ3CwOlYhTtwPc5LY/WeUYEcwFZ8mECPwVSQk4IlqFMSyHrcgDnNWDmnIXyG17Ed5cngaoLlrLQIT+J+zJsmULUG0Y1XcbCe9SaA5aAux8tYrYElb1Ij2JJeur8W3Lg/zEGtSm/VWTBQtb7Dg9hZDTcGVmWpdwuG+ls4/TkmWn8H7sXPebisatJivFIw1F7ciRvxvtitea3ES2jPD2Y5a7WJNV9dINQQhp2Ia+vxXg24m/EossrKZbklXIP7CoQq4S58irewQuzIx2vAZViHayoDre1rEVH7RSwvCGqPcOrP0Yvz8RC+x6/4Gufi0tw9c3AaNrevVa742LWKW8I9eABfpesjeA7f4M1kuQN4VnL4nG7CVRUTZrjN9CN6XahS19guq4ZbLpZypOr++bilAtYhUk7hUBVVwfUIn3tbFADVWoOFmYhbHTMFVaUjeAcLRN6tlVGWojPDlaJKmDGo3M5fghdwwSTPm4sVbVj2P6BKYvc1CrVR8qFJlGFZJqL9yQJV0eJMxLCTCQrmZk1MrqgSPGcKCkYz/NOkpZpx9OlAwXAb9jc4+bcEtXWGoWBfhp8amHgMv+N4vQkFQpXRn4nEOtVy7sNreATXVYEUCUWUSrvaRFk7YPLi8Ci+TICPiTy3LQ9XEBTsRl/Fx3qmADsslnG7SCMb0vi2gqEkAxxsS2//Ee4W6aCWDjnhX73pe4OoDgYKhBrEx0Q5Dd+KZvT2OjcMG+/4vaKoewpnSX5XgD7BrjzYUbwhisUFNW7oEP3A/PSZl6zbKZriIrRf1GrHS12p7MgdljyPpxXf4E6lMp4R5b1SV+qSUrAsi7Z9yyxDET7enWOZ0FcO4gnRIc+WeoSvDuUHx8ByKaZfJOnZgOsRbdveKobxFsv9sBP3YLOJDUMR+hcfiD523NFARVMdQ7WL7nkdFhYE9YdIb90YqtcfNHJw14qr8bDo+6Z7TnZAxKmNYkVGmj64q2O9U3EFbhUdzlIRy+oVm2URmHfjC2zCD1KcmkpNxasE2YKzcYk4IL5I5MozRXo7JEqkXxJIH/7CaDOHw/8BwN0RTQ5fFN4AAAAldEVYdGRhdGU6Y3JlYXRlADIwMTgtMDktMjBUMTE6MzA6MzMtMDQ6MDD2YkMTAAAAJXRFWHRkYXRlOm1vZGlmeQAyMDE4LTA5LTIwVDExOjMwOjMzLTA0OjAwhz/7rwAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAAASUVORK5CYII=" width="15px;height:auto;" />
                        <img v-on:click="deleteColumn(person)" class="pointer" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABUAAAAUCAYAAABiS3YzAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAqxJREFUeNqclFuLUlEUx885ozOlMQ7eLw++iJGX1wyCqZgIsnrwhoIEYZc3YSrqCxR9AZ/mRbzV5PQkBH2DHuzBSyWa0oNRUInXQcd0tLVlb9vqcYQ2LM45++z9O2v91/9shpkdLPN/g+V74HCg5zHEKBgMjlaRQqHQwj4UZIKTy+UbTqfzLNxvQAjxhlVAIVrvcrnO4fsJbw1DBR6P55pCoXiu0+kqxWLxN/pyOp0e22y28QnA0z6f75JarX5hNBor+Xz+G8qUQLlcLvfdarVuyWSy+xqNplIqlX7xgWkgJLKtUqmedjqd17FY7B3MDRGUaIrg62hhIBB4IBaLr1er1WepVOoDzB1BDLBmLA2EDJ+02+1kNBp9BXNdiD8kUzKQyONMJvPRbDZLQIq7dMZ4jWAJsIc/PCIZMtBpBsok4FE2m83PgWsYeMrtdm/D3DwQZXjMUGUzoNkkloEhvpbL5bbD4bgIjXzcarX2QcN9PuAMlFwp8BjAn0wm0xaUesdgMJyB6+1Go5GMx+NJoiFUeYz3TMeCF2ERMTHSaBCJRBLdbvczuOIWZPg+kUik8Lsh0XB+cCf8dqiKNSj5vEgksvb7/S8SieSC3W4343fcst96pnweH17WarW79Xr9ADLeAynESApoVJn2MeWORegc8AoAHqGmQMlIwx5onIHmbfLYbQY8hdJAr9e7A3/Kw2az+ZJqSh91mcduC+AJFEpgKeBVpVK5i4EHGDjAtlmwG8hThrPiJ37/D4pFF/r9/h04rVDJCQC+IUBkGz4fWyyWTVh/T6/XFwuFwg9iRY4+V6G7Nw4PD6Ng7CQN5LEbMnw3HA7v9Xq9t2C3m7QT6ENaIJVK16HTI1zKkAbOHX0s2YOqhWzZWq1G/qzpKcVSgcZ41cmPwTN7SPwVYAArcoAyG20N/QAAAABJRU5ErkJggg==" width="15px;height:auto;" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="formwrapper-inner" v-cloak style="display:none;">
            <div id="addPerson">
                <h3>Add Person</h3>
                <table class="table">
                    <tbody>
                    <tr>
                        <td v-for="(value, key) in people[0]" style="white-space: nowrap;" v-bind:class="{ hide : key == 'id' }">
                            {{ key | ucWords  }} <input v-model="personToAdd[key]" class="form-control" value="" type="text" style="display:inline;width:calc(100% - 50px);margin-left:10px"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button v-on:click="addPerson" class="btn" style="width:100%;">Add Person</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script>
<script type="application/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
<script type="application/javascript">
    let pageApp = new Vue({

        el: '#app',

        computed: {
            totalPages: function() {
                return this.pageTotal;
            },

            orderedPeople: function() {
                let objSortedPeople = this.sortedPeople;

                return objSortedPeople;
            },

            sortedPeople: function () {
                let objOrderedPeople = _.orderBy(this.people, this.orderKey, this.sortByType ? 'asc' : 'desc');

                let intStartIndex = ((this.pageIndex-1) * this.pageDisplay);
                let intIndexOffset = this.people.length - intStartIndex;
                let intEndIndex = intStartIndex + (( this.pageDisplay <= intIndexOffset ) ? this.pageDisplay : intIndexOffset);

                var self = this;

                if (!self.searchQuery) {
                    var intTotalPages = 1;

                    if (this.pageDisplay < objOrderedPeople.length) {
                        intTotalPages = objOrderedPeople.length / this.pageDisplay;
                    }

                    this.pageTotal = Math.ceil(intTotalPages);

                    return objOrderedPeople.slice(intStartIndex, intEndIndex);
                }

                let objFilteredPeople = objOrderedPeople.filter(function (person) {
                    var searchRegex = new RegExp(self.searchQuery, 'i');
                    if ( searchRegex.test(person.user_id) ||  searchRegex.test(person.username) || searchRegex.test(person.first_name) || searchRegex.test(person.last_name) || searchRegex.test(person.created_on)) {
                        return person;
                    }
                });

                let intOrderedIndexOffset = objOrderedPeople.length - intStartIndex;
                let intOrderedEndIndex = intStartIndex + (( this.pageDisplay <= intOrderedIndexOffset ) ? this.pageDisplay : intOrderedIndexOffset);

                if ( objFilteredPeople.length < intStartIndex ) {

                    intStartIndex = Math.floor(objFilteredPeople.length/this.pageDisplay)*this.pageDisplay;
                    this.pageIndex =  Math.ceil(objFilteredPeople.length / this.pageDisplay);
                    intOrderedIndexOffset = objFilteredPeople.length - intStartIndex;
                    intOrderedEndIndex = intStartIndex + intOrderedIndexOffset;
                }

                var intTotalFilteredPages = 1;

                if (this.pageDisplay < objFilteredPeople.length) {
                    intTotalFilteredPages = objFilteredPeople.length / this.pageDisplay;
                }

                this.pageTotal = Math.ceil(intTotalFilteredPages);

                return objFilteredPeople.slice(intStartIndex, intOrderedEndIndex);
            }
        },

        filters: {
            ucWords: function(str) {
                return str.replace("_"," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },

            orderBy: function(type) {

            }
        },

        methods: {
            orderByColumn: function(column) {

                this.sortByType = ( this.orderKey == column ) ? ! this.sortByType : this.sortByType;

                this.orderKey = column;
            },

            addPerson: function() {
                var strUserName = this.personToAdd.username;
                var strFirstName = this.personToAdd.first_name;
                var strLastName = this.personToAdd.last_name;
                var strStatus = this.personToAdd.status;
                var strCreatedOn = this.personToAdd.created_on;

                if (!strFirstName || !strLastName) {
                    return;
                }

                var intId = this.people.length + 1;

                this.people.push({user_id: intId, username: strUserName,  first_name: strFirstName, last_name: strLastName, status: strStatus, created_on: strCreatedOn});

                this.personToAdd.username = "";
                this.personToAdd.first_name = "";
                this.personToAdd.last_name = "";
                this.personToAdd.status = "";
                this.personToAdd.created_on = "";
                this.personToAdd.user_id = "";
            },

            editColumn: function(person) {
                // if(this.personToAdd.user_id) {
                //     return;
                // }
                //
                // this.personToAdd.id = person.user_id;
                // this.personToAdd.username = person.username;
                // this.personToAdd.first_name = person.first_name;
                // this.personToAdd.last_name = person.last_name;
                // this.personToAdd.status = person.status;
                // this.personToAdd.created_on = person.created_on;
                // this.deleteColumn(person);
            },

            deleteColumn: function(person) {
                this.people = this.people.filter(function (curPerson) {
                    return person.user_id != curPerson.user_id;
                });
            },

            prevPage: function() {
                this.pageIndex--;

                this.people = this.people;
            },

            nextPage: function() {
                this.pageIndex++;

                this.people = this.people;
            }
        },

        data: {


            orderKey: 'name',

            sortByType: true,

            columns: ['user_id', 'username', 'first_name', 'last_name', 'status', 'created_on'],

            personToAdd: {user_id: "", first_name: "", last_name: "", status: ""},

            searchQuery: '',

            pageDisplay: 15,

            pageTotal: 1,

            pageIndex: 1,

            people: <?php echo $objActiveBrandPartners->getData()->ConvertToJavaScriptArray(["user_id","@division_id","@company_id","username","created_on","status","first_name","last_name","display_name"]) . PHP_EOL; ?>
        }
    });

</script>


