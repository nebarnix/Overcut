# Overcut
PHP Script to insert G-code lines to aid in over-cutting of closed features in vinyl cutters using Marlin

This tool was created because I wanted a quick and dirty processor to create over-cut for DYI vinyl cutters. I don't have time to do this on-the-fly in the marlin firmware itsself. This will break any open loops, but why would you cut open loops anyway? 

TODO:
[ ] Check that endpoint is the same as the start point to it doesn't break open loops or relief cuts
[ ] Maybe someday G2/G3 support??
