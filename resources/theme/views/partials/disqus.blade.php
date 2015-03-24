@if(Config::get('laradic_docit.disqus.enabled'))
<div class="box">

  <header>
      <i class="fa fa-comments"></i>
      <h3>Comments</h3>
  </header>

  <section class="with-padding">

      <div id="disqus_thread"></div>
      <script type="text/javascript">
          /* * * CONFIGURATION VARIABLES * * */
          var disqus_shortname = '{{ Config::get('laradic_docit.disqus.shortname') }}';

          /* * * DON'T EDIT BELOW THIS LINE * * */
          (function() {
              var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
              dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
              (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
          })();
      </script>
      <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
  </section>

</div>
@endif
