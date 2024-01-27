jQuery(function($){
	$(document).ready(function(){
		$(".wrap-traffic-logs-content").each(function(){
			var wrap = $(this);
			var pairsEl = wrap.find(".traffic-logs-content-pairs");
			var addEl = wrap.find(".traffic-logs-content-add");

			addEl.on("click", function(e){
				e.preventDefault();
				pairsEl.append(LinkData.pairTpl);
			});
		});

		$(document).on("click", ".traffic-logs-content-del", function(e){
			e.preventDefault();
			$(this).closest(".traffic-logs-content-pair").remove();
		});
		
        
        $('.traffic-logs-content-clear-log').on('click', function(e){
            e.preventDefault();
            var isConfirmed = confirm("Are you sure you want to clear logs?");
            if(isConfirmed){
                window.location.href = $(this).attr('href');
            }
        });


	});
});