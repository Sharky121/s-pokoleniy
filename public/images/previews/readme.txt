#!/bin/sh

for fullfile in `find ../ -not -name '*thumb*' -and -name '*.jpg'`; do
    filename=$(basename -- "$fullfile")
    ffmpeg -i $fullfile -y -an -q 0 -vf scale="'if(gt(iw,ih),-1,90):if(gt(iw,ih),90,-1)', crop=90:90:exact=1" "./$filename"
done;