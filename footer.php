<script>
    if(document.getElementById('loginForm')){

        //-=-=- Don't set/start session timers when on login form
        setLoginFieldFocus();
    }
    else{

        document.onkeypress = resetTimer;
        document.onmousemove = resetTimer;
    }
</script>
</body>
</html>
