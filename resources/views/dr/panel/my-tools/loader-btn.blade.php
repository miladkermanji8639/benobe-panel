<style>
 /* src\features\login\loading\loading.css*/
 .loader {
  border: 4px solid #f3f3f3;
  /* Light grey */
  border-top: 4px solid #3498db;
  /* Blue */
  border-radius: 50%;
  width: 20px;
  height: 20px;
  animation: spin 2s linear infinite;
  display: none;
 }

 @keyframes spin {
  0% {
   transform: rotate(0deg);
  }

  100% {
   transform: rotate(360deg);
  }
 }
</style>
