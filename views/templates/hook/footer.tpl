{if isset($cm_tawkto_profile_id) && $cm_tawkto_widget_id}
    <!-- Start of Tawk.to Script -->
    {literal}
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    {/literal}
    s1.src='https://embed.tawk.to/{$cm_tawkto_profile_id}/{$cm_tawkto_widget_id}';
    {literal}
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
    {/literal}
    <!-- End of Tawk.to Script -->
{/if}