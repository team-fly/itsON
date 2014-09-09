/*
 * Copyright (C) 2014 Lucas Rocha
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package org.lucasr.twowayview;

import android.content.Context;
import android.util.AttributeSet;

public class TWStaggeredGridView extends TWView {
    private static final String LOGTAG = "TWStaggeredGridView";

    public TWStaggeredGridView(Context context) {
        this(context, null);
    }

    public TWStaggeredGridView(Context context, AttributeSet attrs) {
        this(context, attrs, 0);
    }

    public TWStaggeredGridView(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        setLayoutManager(new TWStaggeredGridLayoutManager(context, attrs, defStyle));
    }
}