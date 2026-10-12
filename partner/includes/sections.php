<?php
return [
  'properties'=>['title'=>'My properties','icon'=>'buildings','description'=>'Review your sample listings and property profile.','columns'=>['Reference','Property','Location','Status'],'rows'=>[
    ['PROP-101','Casa do Mar','Lisbon, Portugal','Published'],['PROP-105','Harbour House','Copenhagen, Denmark','Review']]],
  'rooms'=>['title'=>'Rooms & availability','icon'=>'door-open','description'=>'Sample room inventory; availability is not live.','columns'=>['Reference','Room type','Property','Sample inventory','Status'],'rows'=>[
    ['ROOM-041','Deluxe King','Casa do Mar','4 rooms','Available'],['ROOM-042','City Twin','Casa do Mar','2 rooms','Available'],['ROOM-045','Harbour Twin','Harbour House','1 room','Review']]],
  'reservations'=>['title'=>'Reservations','icon'=>'calendar-check','description'=>'Example guest itineraries for frontend demonstration.','columns'=>['Reference','Guest','Property','Dates','Status'],'rows'=>[
    ['DEMO-80412','Taylor Morgan','Casa do Mar','12–15 May','Confirmed'],['DEMO-72951','Jordan Lee','Casa do Mar','18–21 Jun','Pending'],['DEMO-41182','Avery Patel','Harbour House','03–05 Sep','Pending']]],
  'pricing'=>['title'=>'Pricing','icon'=>'tags','description'=>'Illustrative rates and rule previews.','columns'=>['Room type','Property','Sample rate','Rule status'],'rows'=>[
    ['Deluxe King','Casa do Mar','$186 / night','Active'],['City Twin','Casa do Mar','$154 / night','Active'],['Harbour Twin','Harbour House','$205 / night','Review']]],
  'reports'=>['title'=>'Earnings & reports','icon'=>'bar-chart-line','description'=>'Example metrics, not actual earnings or financial reporting.','columns'=>['Report','Period','Sample amount','Status'],'rows'=>[
    ['Reservation summary','This month','$8,420','Demo only'],['Occupancy overview','Last 30 days','72%','Sample'],['Payout preview','Next cycle','$2,180','Not payable']]],
  'reviews'=>['title'=>'Guest reviews','icon'=>'star','description'=>'Illustrative ratings and moderation status.','columns'=>['Reference','Property','Rating','Status'],'rows'=>[
    ['REV-081','Casa do Mar','5.0 / 5','Published'],['REV-082','Casa do Mar','4.8 / 5','Review'],['REV-085','Harbour House','4.7 / 5','Published']]],
  'staff'=>['title'=>'Authorized staff','icon'=>'person-badge','description'=>'Example team access roster; no real permissions are managed.','columns'=>['Reference','Name','Role','Status'],'rows'=>[
    ['STAFF-11','Taylor Morgan','Property manager','Active'],['STAFF-12','Jordan Lee','Front desk','Active'],['STAFF-13','Avery Patel','Housekeeping','Pending']]],
];
