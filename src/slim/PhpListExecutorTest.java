// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static org.junit.Assert.assertEquals;
import static slim.TestSuite.getTestIncludePath;
import static util.ListUtility.list;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;

import fitnesse.slim.ListExecutor;
import fitnesse.slim.ListExecutorTestBase;
import fitnesse.slim.SlimClient;
import fitnesse.slim.SlimFactory;

public class PhpListExecutorTest extends ListExecutorTestBase {

  private static SlimFactory slimFactory;

  @BeforeClass
  public static void setUpClass() {
    slimFactory = new PhpSlimFactory(getTestIncludePath());
  }

  @AfterClass
  public static void tearDownClass() {
    slimFactory.stop();
  }

  @Override
  protected ListExecutor getListExecutor() throws Exception {
    return slimFactory.getListExecutor(false);
  }

  @Override
  protected String getTestClassPath() {
    return "testModule";
  }

  @Override
  protected void respondsWith(List<Object> expected) {
    expectedResults.addAll(expected);
    List<Object> result = executor.execute(statements);
    Map<String, Object> expectedMap = SlimClient.resultToMap(expectedResults);
    Map<String, Object> resultMap = removeProxies(SlimClient
        .resultToMap(result));
    assertEquals(expectedMap, resultMap);
  }

  private Map<String, Object> removeProxies(Map<String, Object> resultMap) {
    Map<String, Object> map = new HashMap<String, Object>();
    for (String string : resultMap.keySet()) {
      map.put(string, removeProxy(resultMap.get(string)));
    }
    return map;
  }

  private Object removeProxy(Object object) {
    if (object == null || object instanceof List) {
      return object;
    }
    return object.toString();
  }

  @Override
  @Test
  public void callToVoidFunctionReturnsVoidValue() throws Exception {
    statements.add(list("id", "call", "testSlim", "voidFunction"));
    // There is no difference in PHP between functions returning null and void.
    respondsWith(list(list("id", null)));
  }
}
