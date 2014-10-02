var disqus_shortname = 'dkeixm';
var disqus_identifier = 'verse';
var disqus_url = 'http://tehverse.io/verse/';
var disqus_title = 'verse';
var disqus_language = 'en';
var bs;
var det={};
var chil="";
$.get('sessionvar.php', function(data){bs=data; console.log("inside fun--"+bs); });


function SearchCtrl($scope, $http) {

	$scope.url = 'search.php'; // The url of our search
	$scope.url2 = 'create_verse.php';
	$scope.url3 = 'addmails.php';
	$scope.url4 = 'uprofile.php';
	$scope.url5 = 'removeverse.php';
	$scope.message="this is message";
	$scope.verse={};
	$scope.mails={};
	$scope.details = false;
	$scope.ralert = false;
	
	var verse_data = [$scope.verse.title, $scope.verse.desc, $scope.verse.origin, $scope.verse.mails, $scope.verse.val];
	
	
	// The function that will be executed on button click (ng-click="search()")
$scope.search = function(para1) {
		//$scope.ralert ?  $scope.ralert= false : $scope.ralert = true;
		$scope.ralert = false;
		$scope.details = true;
		$scope.mails.vid=para1;
		document.getElementById("tree-container").innerHTML = "";
		
// Get JSON data
// beginning of graph generation

	treeJSON = d3.json("http://theverse.io/verse/db.php?hari="+para1, function(error, treeData) {

   
	console.log("got the data from db");
	// Calculate total nodes, max label length
    var totalNodes = 0;
    var maxLabelLength = 0;
    // variables for drag/drop
    var selectedNode = null;
    var draggingNode = null;
    // panning variables
    var panSpeed = 200;
    var panBoundary = 20; // Within 20px from edges will pan when dragging.
    // Misc. variables
    var i = 0;
    var duration = 750;
    var root;

    // size of the diagram
    var viewerWidth = $(document).width()*3.17/4;
    var viewerHeight = $(document).height()*3/4;
	
    var tree = d3.layout.tree()
        .size([viewerHeight, viewerWidth]);

    // define a d3 diagonal projection for use by the node paths later on.
    var diagonal = d3.svg.diagonal()
        .projection(function(d) {
            return [d.y, d.x];
        });

    // A recursive helper function for performing some setup by walking through all nodes

    function visit(parent, visitFn, childrenFn) {
        if (!parent) return;

        visitFn(parent);

        var children = childrenFn(parent);
        if (children) {
            var count = children.length;
            for (var i = 0; i < count; i++) {
                visit(children[i], visitFn, childrenFn);
            }
        }
    }

    // Call visit function to establish maxLabelLength
    visit(treeData, function(d) {
        totalNodes++;
        maxLabelLength = Math.max(d.name.length, maxLabelLength);

    }, function(d) {
        return d.children && d.children.length > 0 ? d.children : null;
    });


    // sort the tree according to the node names

    function sortTree() {
        tree.sort(function(a, b) {
            return b.name.toLowerCase() < a.name.toLowerCase() ? 1 : -1;
        });
    }
    // Sort the tree initially incase the JSON isn't in a sorted order.
    sortTree();

    // TODO: Pan function, can be better implemented.

    function pan(domNode, direction) {
        var speed = panSpeed;
        if (panTimer) {
            clearTimeout(panTimer);
            translateCoords = d3.transform(svgGroup.attr("transform"));
            if (direction == 'left' || direction == 'right') {
                translateX = direction == 'left' ? translateCoords.translate[0] + speed : translateCoords.translate[0] - speed;
                translateY = translateCoords.translate[1];
            } else if (direction == 'up' || direction == 'down') {
                translateX = translateCoords.translate[0];
                translateY = direction == 'up' ? translateCoords.translate[1] + speed : translateCoords.translate[1] - speed;
            }
            scaleX = translateCoords.scale[0];
            scaleY = translateCoords.scale[1];
            scale = zoomListener.scale();
            svgGroup.transition().attr("transform", "translate(" + translateX + "," + translateY + ")scale(" + scale + ")");
            d3.select(domNode).select('g.node').attr("transform", "translate(" + translateX + "," + translateY + ")");

            zoomListener.scale(zoomListener.scale());
            zoomListener.translate([translateX, translateY]);
            panTimer = setTimeout(function() {
                pan(domNode, speed, direction);
            }, 50);
        }
    }

    // Define the zoom function for the zoomable tree

    function zoom() {
        svgGroup.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }


    // define the zoomListener which calls the zoom function on the "zoom" event constrained within the scaleExtents
    var zoomListener = d3.behavior.zoom().scaleExtent([0.1, 3]).on("zoom", zoom);

    function initiateDrag(d, domNode) {
        draggingNode = d;
        d3.select(domNode).select('.ghostCircle').attr('pointer-events', 'none');
        d3.selectAll('.ghostCircle').attr('class', 'ghostCircle show');
        d3.select(domNode).attr('class', 'node activeDrag');

        svgGroup.selectAll("g.node").sort(function(a, b) { // select the parent and sort the path's
            if (a.id != draggingNode.id) return 1; // a is not the hovered element, send "a" to the back
            else return -1; // a is the hovered element, bring "a" to the front
        });
        // if nodes has children, remove the links and nodes
        if (nodes.length > 1) {
            // remove link paths
            links = tree.links(nodes);
            nodePaths = svgGroup.selectAll("path.link")
                .data(links, function(d) {
                    return d.target.id;
                }).remove();
            // remove child nodes
            nodesExit = svgGroup.selectAll("g.node")
                .data(nodes, function(d) {
                    return d.id;
                }).filter(function(d, i) {
                    if (d.id == draggingNode.id) {
                        return false;
                    }
                    return true;
                }).remove();
        }

        // remove parent link
        parentLink = tree.links(tree.nodes(draggingNode.parent));
        svgGroup.selectAll('path.link').filter(function(d, i) {
            if (d.target.id == draggingNode.id) {
                return true;
            }
            return false;
        }).remove();

        dragStarted = null;
    }

    // define the baseSvg, attaching a class for styling and the zoomListener
    var baseSvg = d3.select("#tree-container").append("svg")
        .attr("width", viewerWidth)
        .attr("height", viewerHeight)
        .attr("class", "overlay")
        .call(zoomListener);


    // Define the drag listeners for drag/drop behaviour of nodes.
    dragListener = d3.behavior.drag()
        .on("dragstart", function(d) {
            if (d == root) {
                return;
            }
            dragStarted = true;
            nodes = tree.nodes(d);
            d3.event.sourceEvent.stopPropagation();
            // it's important that we suppress the mouseover event on the node being dragged. Otherwise it will absorb the mouseover event and the underlying node will not detect it d3.select(this).attr('pointer-events', 'none');
        })
        .on("drag", function(d) {
            if (d == root) {
                return;
            }
            if (dragStarted) {
                domNode = this;
                initiateDrag(d, domNode);
            }

            // get coords of mouseEvent relative to svg container to allow for panning
            relCoords = d3.mouse($('svg').get(0));
            if (relCoords[0] < panBoundary) {
                panTimer = true;
                pan(this, 'left');
            } else if (relCoords[0] > ($('svg').width() - panBoundary)) {

                panTimer = true;
                pan(this, 'right');
            } else if (relCoords[1] < panBoundary) {
                panTimer = true;
                pan(this, 'up');
            } else if (relCoords[1] > ($('svg').height() - panBoundary)) {
                panTimer = true;
                pan(this, 'down');
            } else {
                try {
                    clearTimeout(panTimer);
                } catch (e) {

                }
            }

            d.x0 += d3.event.dy;
            d.y0 += d3.event.dx;
            var node = d3.select(this);
            node.attr("transform", "translate(" + d.y0 + "," + d.x0 + ")");
            updateTempConnector();
        }).on("dragend", function(d) {
            if (d == root) {
                return;
            }
            domNode = this;
            if (selectedNode) {
                // now remove the element from the parent, and insert it into the new elements children
                var index = draggingNode.parent.children.indexOf(draggingNode);
                if (index > -1) {
                    draggingNode.parent.children.splice(index, 1);
                }
                if (typeof selectedNode.children !== 'undefined' || typeof selectedNode._children !== 'undefined') {
                    if (typeof selectedNode.children !== 'undefined') {
                        selectedNode.children.push(draggingNode);
                    } else {
                        selectedNode._children.push(draggingNode);
                    }
                } else {
                    selectedNode.children = [];
                    selectedNode.children.push(draggingNode);
                }
                // Make sure that the node being added to is expanded so user can see added node is correctly moved
                expand(selectedNode);
                sortTree();
                endDrag();
            } else {
                endDrag();
            }
        });

    function endDrag() {
        selectedNode = null;
        d3.selectAll('.ghostCircle').attr('class', 'ghostCircle');
        d3.select(domNode).attr('class', 'node');
        // now restore the mouseover event or we won't be able to drag a 2nd time
        d3.select(domNode).select('.ghostCircle').attr('pointer-events', '');
        updateTempConnector();
        if (draggingNode !== null) {
            update(root);
            centerNode(draggingNode);
            draggingNode = null;
        }
    }

    // Helper functions for collapsing and expanding nodes.

    function collapse(d) {
        if (d.children) {
            d._children = d.children;
            d._children.forEach(collapse);
            d.children = null;
        }
    }

    function expand(d) {
        if (d._children) {
            d.children = d._children;
            d.children.forEach(expand);
            d._children = null;
        }
    }

    var overCircle = function(d) {
        selectedNode = d;
        updateTempConnector(); 
		//popover asd
    };
    var outCircle = function(d) {
        selectedNode = null;
        updateTempConnector();
    };

    // Function to update the temporary connector indicating dragging affiliation
    var updateTempConnector = function() {
        var data = [];
        if (draggingNode !== null && selectedNode !== null) {
            // have to flip the source coordinates since we did this for the existing connectors on the original tree
            data = [{
                source: {
                    x: selectedNode.y0,
                    y: selectedNode.x0
                },
                target: {
                    x: draggingNode.y0,
                    y: draggingNode.x0
                }
            }];
        }
        var link = svgGroup.selectAll(".templink").data(data);

        link.enter().append("path")
            .attr("class", "templink")
            .attr("d", d3.svg.diagonal())
            .attr('pointer-events', 'none');

        link.attr("d", d3.svg.diagonal());

        link.exit().remove();
    };

    // Function to center node when clicked/dropped so node doesn't get lost when collapsing/moving with large amount of children.

    function centerNode(source) {
        scale = zoomListener.scale();
        x = -source.y0;
        y = -source.x0;
        x = x * scale + viewerWidth / 2;
        y = y * scale + viewerHeight / 2;
        d3.select('g').transition()
            .duration(duration)
            .attr("transform", "translate(" + x + "," + y + ")scale(" + scale + ")");
        zoomListener.scale(scale);
        zoomListener.translate([x, y]);
    }

    // Toggle children function

    function toggleChildren(d) {
        if (d.children) {
            d._children = d.children;
            d.children = null;
        } else if (d._children) {
            d.children = d._children;
            d._children = null;
        }
        return d;
    }

    // Toggle children on click.

    function click(d) {
        if (d3.event.defaultPrevented) return; // click suppressed
      d = toggleChildren(d);
        update(d);
 		centerNode(d);
    }



function mouseover(d){

		//setdet(d);
		chil='';
		var len=d.children;
		console.log("len"+len.length);
		
		for(var i=0;i<len.length;i++){
		chil = chil+len[i].name.substring(0,4);
		console.log("childrens are-"+chil);
		}
		console.log(chil);
		//console.log("lenght is"+d.children[2].name);
		var sef=this;
	$http.post("getdesc.php", { "mail" : d.name, "children" : chil}).
		success(function(data, status) {

			det.name = data[0].name;
			det.link = data[0].link; // Show result from server in our <pre></pre> element
			det.desc = data[0].desc;
			det.msg = data[0].msg;
			console.log("name-"+det.name);
			console.log("link-"+det.link);
			console.log("desc-"+det.desc);
			console.log("msg-"+det.msg);
			console.log("this is"+sef);
			d3.select(sef).popover(function(d,i){
	
	//var matrix = this.getScreenCTM()
      //  .translate(+ this.getAttribute("cx"), + this.getAttribute("cy"));
	
	//console.log((window.pageXOffset + matrix.e) + "px");
	//console.log((window.pageYOffset + matrix.f) + "px");
	
	// Create the http post request
		// the data holds the keywords
		// The request is a JSON request.
	
	s=det.desc;
	
	svg=d3.select(document.createElement("svg")).attr("height",150).attr("width",240);
	g=svg.append("g").attr('x',0).attr('y',0);
	var poy=7;
	//console.log("inside loop"+s);
	g.append("text").text("About Me").attr('x', 90).attr('y',10).attr('font-size','12px').attr('font-weight','bold');//.attr("text-anchor", "middle");
	
	do{ 
	console.log("inside loop"+s.substring(0,27));
	g.append("text").text(s).attr('x', 90).attr('y',poy+13).attr('font-size','12px');//.attr("text-anchor", "middle");
	poy=poy+13;

	}while((s = s.substring(27, s.length))!= "");
	
	/*
	g.append("text").text(det.desc).attr('x', 90).attr('y',20).attr('font-size','12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 90).attr('y',33).attr('font-size','12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 90).attr('y',46).attr('font-size','12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 90).attr('y',59).attr('font-size','12px');//.attr("text-anchor", "middle");
	*/
	g.append("text").text("Message").attr('x', 0).attr('y',89).attr('font-size','12px').attr('font-weight','bold');//.attr("text-anchor", "middle");
	
var s2=det.msg;	/*
var p=43, poy2=83;
	//console.log("inside loop"+s);
	*/
	var poy2=89;
	do{ 
	console.log("inside loop"+s2.substring(0,43));
	g.append("text").text(s2.substring(0,43)).attr('x', 0).attr('y', poy2+13).attr('font-size', '12px');//.attr("text-anchor", "middle");
	poy2=poy2+13;

	}while((s2 = s2.substring(43, s2.length))!= "");
	
	/*
	g.append("text").text(det.desc).attr('x', 0).attr('y',90).attr('font-size', '12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 0).attr('y',103).attr('font-size', '12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 0).attr('y',116).attr('font-size', '12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 0).attr('y',129).attr('font-size', '12px');//.attr("text-anchor", "middle");
	g.append("text").text(det.desc).attr('x', 0).attr('y',142).attr('font-size', '12px');//.attr("text-anchor", "middle");
*/
	//g.append("text").text(det.desc).attr("dy", "30");
	g.append("image").attr("width", 80).attr("height", 80).attr('x',0).attr('y',0).attr("xlink:href",det.link);
	
	//var point = d3.mouse(this), p = {x: point[0], y: point[1] };
	//console.log(p);
	//console.log(d);
	//console.log(d3.event.pageX);
	//console.log(d3.event.pageY);
	//console.log(window.event.clientX+"and"+window.event.clientY);
	//console.log(d.x+"and"+d.y);
	//$.get('udetails.php', function(data){det.name=data.name; det.desc=data.name; });
	
	return {
	type: "popover",
      title: det.name,
      content: svg,
      detection: "shape",
      placement: "fixed",
      gravity: "right",
      position: [0,0],//[viewerHeight-d.y, d.x],
      displacement: [0, 0],
      mousemove: false
    };

	});
      
		
	//svg.selectAll("*").remove();
	//$("#svg").empty();
	var svg="";	 

			//getpop(d);
}).
		error(function(data, status) {
			$scope.data = data || "Request failed";
			$scope.status = status;			
		});
		
		
	}
    function update(source) {
        // Compute the new height, function counts total children of root node and sets tree height accordingly.
        // This prevents the layout looking squashed when new nodes are made visible or looking sparse when nodes are removed
        // This makes the layout more consistent.
        var levelWidth = [1];
        var childCount = function(level, n) {

            if (n.children && n.children.length > 0) {
                if (levelWidth.length <= level + 1) levelWidth.push(0);

                levelWidth[level + 1] += n.children.length;
                n.children.forEach(function(d) {
                    childCount(level + 1, d);
                });
            }
        };
        childCount(0, root);
        var newHeight = d3.max(levelWidth) * 25; // 25 pixels per line  
        tree = tree.size([newHeight, viewerWidth]);

        // Compute the new tree layout.
        var nodes = tree.nodes(root).reverse(),
            links = tree.links(nodes);

        // Set widths between levels based on maxLabelLength.
        nodes.forEach(function(d) {
            d.y = (d.depth * (maxLabelLength * 10)); //maxLabelLength * 10px
            // alternatively to keep a fixed scale one can set a fixed depth per level
            // Normalize for fixed-depth by commenting out below line
            // d.y = (d.depth * 500); //500px per level.
        });

        // Update the nodes…
        node = svgGroup.selectAll("g.node")
            .data(nodes, function(d) {
                return d.id || (d.id = ++i);
            });

        // Enter any new nodes at the parent's previous position.
        var nodeEnter = node.enter().append("g")
            .call(dragListener)
            .attr("class", "node")
			.style("fill", function(d) {
               return "lightsteelblue"; // return d.name == bs ? "Green" : "lightsteelblue";
            })
            .attr("transform", function(d) {
                return "translate(" + source.y0 + "," + source.x0 + ")";
            })
            .on('click', click)
			.on('mouseover',mouseover);

        nodeEnter.append("circle")
            .attr('class', 'nodeCircle')
            .attr("r", 0)
			.style("stroke", function(d){ return d.name == bs ? "Green" : "lightsteelblue" ;}) //indianred
			.style("fill", function(d) {
				
                return d._children ? "lightsteelblue" : "#fff";
            });

        nodeEnter.append("text")
            .attr("x", function(d) {
                return d.children || d._children ? -10 : 10;
            })
            .attr("dy", ".35em")
            .attr('class', 'nodeText')
            .attr("text-anchor", function(d) {
                return d.children || d._children ? "end" : "start";
            })
            .text(function(d) {
                return d.name;
            })
            .style("fill", "darkslategrey");
			
		/* appending images
			nodeEnter.append("svg:image")
			.attr("class","circle")
			.attr("xlink:href","verse.png");
			*/ 

        // phantom node to give us mouseover in a radius around it
        nodeEnter.append("circle")
            .attr('class', 'ghostCircle')
            .attr("r", 30)
            .attr("opacity", 0.2) // change this to zero to hide the target area
        .style("fill", "red")
            .attr('pointer-events', 'mouseover')
            .on("mouseover", function(node) {
                overCircle(node);
            })
            .on("mouseout", function(node) {
                outCircle(node);
            });

        // Update the text to reflect whether node has children or not.
        node.select('text')
            .attr("x", function(d) {
                return d.children || d._children ? -10 : 10;
            })
            .attr("text-anchor", function(d) {
                return d.children || d._children ? "end" : "start";
            })
            .text(function(d) {
                return d.name;
            });

        // Change the circle fill depending on whether it has children and is collapsed
        node.select("circle.nodeCircle")
            .attr("r", 4.5)
            .style("fill", function(d) {
                return d._children ? "lightsteelblue" : "#fff";
            });

        // Transition nodes to their new position.
        var nodeUpdate = node.transition()
            .duration(duration)
            .attr("transform", function(d) {
                return "translate(" + d.y + "," + d.x + ")";
            });

        // Fade the text in
        nodeUpdate.select("text")
            .style("fill-opacity", 1);

        // Transition exiting nodes to the parent's new position.
        var nodeExit = node.exit().transition()
            .duration(duration)
            .attr("transform", function(d) {
                return "translate(" + source.y + "," + source.x + ")";
            })
            .remove();

        nodeExit.select("circle")
            .attr("r", 0);

        nodeExit.select("text")
            .style("fill-opacity", 0);

        // Update the links…
        var link = svgGroup.selectAll("path.link")
            .data(links, function(d) {
                return d.target.id;
            });

        // Enter any new links at the parent's previous position.
        link.enter().insert("path", "g")
            .attr("class", "link")
            .attr("d", function(d) {
                var o = {
                    x: source.x0,
                    y: source.y0
                };
                return diagonal({
                    source: o,
                    target: o
                });
            });

        // Transition links to their new position.
        link.transition()
            .duration(duration)
            .attr("d", diagonal);

        // Transition exiting nodes to the parent's new position.
        link.exit().transition()
            .duration(duration)
            .attr("d", function(d) {
                var o = {
                    x: source.x,
                    y: source.y
                };
                return diagonal({
                    source: o,
                    target: o
                });
            })
            .remove();

        // Stash the old positions for transition.
        nodes.forEach(function(d) {
            d.x0 = d.x;
            d.y0 = d.y;
        });
    }

    // Append a group which holds all nodes and which the zoom Listener can act upon.
    var svgGroup = baseSvg.append("g");

    // Define the root
    root = treeData;
    root.x0 = viewerHeight / 2;
    root.y0 = 0;

    // Layout the tree initially and center on the root node.
    update(root);
    centerNode(root);
	});

		
		
		// Create the http post request
		// the data holds the keywords
		// The request is a JSON request.
		$http.post($scope.url, { "data" : para1}).
		success(function(data, status) {
		
			$scope.status = status;
			$scope.data = data[0];
			$scope.title = data[0].title; // Show result from server in our <pre></pre> element
			$scope.cause = data[0].cause;
			$scope.origin = data[0].origin;
			$scope.mails.origin=data[0].origin;
			
		/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
   	
		
		var r=$scope.title+$scope.origin;
		var u=$scope.title.replace(/[^a-zA-Z0-9]/g, "");
		var disqus_shortname = r.replace(/[^a-zA-Z0-9]/g, ""); // required: replace example with your forum shortname
		console.log(disqus_shortname);
		$scope.short_name=disqus_shortname;
		//window.disqus_shortname=disqus_shortname;
		
		disqus_identifier=u;
		disqus_url="http://theverse.io/verse/#!"+u;
		disqus_title=$scope.title;
		console.log(disqus_identifier+"-and-"+disqus_url+"-and-"+disqus_title);
		
		DISQUS.reset( {
	reload: true,
	config: function () {  
    this.page.identifier = disqus_identifier;  
    this.page.url = disqus_url;
	this.page.title= disqus_title;
  console.log("inside reset");
  }
});
		
		/* * * DON'T EDIT BELOW THIS LINE * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })(); 
   	    }).
		
		error(function(data, status) {
			$scope.data = data || "Request failed";
			$scope.status = status;			
		});
	};
	

		function check(a)
    {
	
	
      var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
      dsq.src = "http://"+a+".disqus.com/embed.js";
      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
      
    }
  
	
	$scope.cverse = function() {
	$http.post($scope.url2, $scope.verse). 
	success(function(data, status) {
	$scope.status=status;
	$scope.data2=data;
	}).
	error(function(data2, status) {
	
	$scope.data2 = data2 || "Request Failed";
	$scope.status=status;
	});
	
	};
	
	$scope.cprofile = function() {
	$http.post($scope.url4, $scope.verse). 
	success(function(data, status) {
	$scope.status=status;
	$scope.data2=data;

	}).
	error(function(data2, status) {
	
	$scope.data2 = data2 || "Request Failed";
	$scope.status=status;
	});
	
	};
	
	$scope.averse = function(mail) {
	$scope.mails.origin=mail;
	console.log("origin is"+mail);
	console.log("in the function");
	$http.post($scope.url3, $scope.mails). 
	success(function(data, status) {
	$scope.status=status;
	$scope.data3=data;
	}).
	error(function(data3, status) {
	
	$scope.data2 = data3 || "Request Failed";
	$scope.status=status;
	});
	
	};
	
	
	$scope.remverse = function(mail) {
	
	$scope.mails.origin=mail;	
	//$scope.ralert = true;
	//console.log($scope.ralert);
	//console.log("vid is --"+$scope.mails.vid+"-- and origin is---"+$scope.mails.origin);
	$http.post($scope.url5, $scope.mails). 
	success(function(data, status) {
	$scope.status=status;
	$scope.data3=data;
	$scope.ralert = true;
	}).
	error(function(data3, status) {
	
	$scope.data2 = data3 || "Request Failed";
	$scope.status=status;
	});
	
	};
	
	
	
	
		
	}
