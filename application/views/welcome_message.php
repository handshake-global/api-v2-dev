<script src="https://www.gstatic.com/firebasejs/3.7.2/firebase.js"></script>
<script>
// Initialize Firebase
var config = {
    apiKey: "AAAAbfDZff8:APA91bFQnxR_aL9cxIMtarJg12ufNKiwsjDOPLb6T4M47O6qsxgHVVt9dhtPF1q6PEDAGWLTPepLH51p646o-SCF20vwmgBZBQEnkvyOro5ac3MMcJ0SRCjQKImhudvDjnVzDMNefXKFXitn5SHUNz2BU-u74lwQnQ",
    messagingSenderId: "472192220671"
  };
firebase.initializeApp(config);

const messaging = firebase.messaging();

messaging.requestPermission()
.then(function() {
  console.log('Notification permission granted.');
  return messaging.getToken();
})
.then(function(token) {
  console.log(token); // Display user token
})
.catch(function(err) { // Happen if user deney permission
  console.log('Unable to get permission to notify.', err);
});

messaging.onMessage(function(payload){
	console.log('onMessage',payload);
})

</script>
