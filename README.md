# Overcut
PHP Script to insert G-code lines to aid in over-cutting of closed features in vinyl cutters using Marlin. 

**Does not support G2/G3 code**

**Gcode must be in absolute coordinate mode **

**About:** This tool was created because I wanted a quick and dirty processor to create over-cut for DYI vinyl cutters. I don't have time to do this on-the-fly in the marlin firmware itsself. This will break any open loops, but why would you cut open loops anyway? 

**Methodology:** The code detects the Z down command, and begins storing points until the total distance between stored points is equal to the required overcut distance. This is almost never the case, instead, some small line segment will be required between two points to exactly equal the overcut distance. Trigonometry is used to solve for a point between two points that makes up the required length to read the overcut distance. 

The formula is the following:

X = $prevPosX - ($distanceToGoal*($prevPosX-$currentPosX)) / $distance;

Y = $prevPosY - ($distanceToGoal*($prevPosY-$currentPosY)) / $distance;

Inserted lines are marked with an ";overcut line!" comment

TODO:
- [ ] Check that endpoint is the same as the start point to it doesn't break open loops or relief cuts
- [ ] Maybe someday G2/G3 support??
- [ ] Customize pen down/up commands
